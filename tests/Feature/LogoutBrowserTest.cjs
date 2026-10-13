const { spawn, execFileSync } = require('node:child_process');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const assert = require('node:assert/strict');
const { createServer } = require('node:net');

const workspace = path.resolve(__dirname, '../..');
const temporary = fs.mkdtempSync(path.join(os.tmpdir(), 'lavea-logout-browser-'));
const database = path.join(temporary, 'database.sqlite');
fs.writeFileSync(database, '');
const environment = { ...process.env, APP_ENV: 'testing', DB_CONNECTION: 'sqlite', DB_DATABASE: database, DB_URL: '', SESSION_DRIVER: 'file', SESSION_COOKIE: 'lavea_logout_browser_test', CACHE_STORE: 'array', QUEUE_CONNECTION: 'sync', LAVEA_BROWSER_VERIFICATION: process.argv.includes('--verification') ? '1' : '0', LAVEA_BROWSER_FULFILLMENT: process.argv.includes('--fulfillment') ? '1' : '0' };
let base;
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const phpPath = process.env.PHP_BINARY || path.join(os.homedir(), '.config/herd/bin/php84/php.exe');
let server, chrome, socket;
const uploadedServiceImages = [];
const pause = ms => new Promise(resolve => setTimeout(resolve, ms));
const availablePort = () => new Promise((resolve, reject) => {
    const listener = createServer();
    listener.once('error', reject);
    listener.listen(0, '127.0.0.1', () => {
        const port = listener.address().port;
        listener.close(() => resolve(port));
    });
});
async function until(check, label) {
    for (let attempt = 0; attempt < 150; attempt++) {
        try { const result = await check(); if (result) return result; } catch {}
        await pause(100);
    }
    throw new Error('Timed out: ' + label);
}

(async () => {
    try {
        const serverPort = await availablePort();
        let debugPort = await availablePort();
        while (debugPort === serverPort) debugPort = await availablePort();
        base = `http://127.0.0.1:${serverPort}`;
        execFileSync(phpPath, ['tests/Feature/LogoutBrowserFixture.php'], { cwd: workspace, env: environment, stdio: 'pipe' });
        server = spawn(phpPath, ['artisan', 'serve', '--host=127.0.0.1', `--port=${serverPort}`, '--no-reload', '--no-interaction'], { cwd: workspace, env: environment, windowsHide: true, stdio: 'ignore' });
        await until(async () => (await fetch(base + '/login')).ok, 'Laravel server');
        chrome = spawn(chromePath, ['--headless=new', `--remote-debugging-port=${debugPort}`, '--user-data-dir=' + path.join(temporary, 'chrome'), '--no-first-run', '--no-default-browser-check', 'about:blank'], { windowsHide: true, stdio: 'ignore' });
        const target = await until(async () => (await (await fetch(`http://127.0.0.1:${debugPort}/json`)).json()).find(tab => tab.type === 'page'), 'Chrome');
        socket = new WebSocket(target.webSocketDebuggerUrl);
        await new Promise((resolve, reject) => { socket.addEventListener('open', resolve); socket.addEventListener('error', reject); });
        let sequence = 0;
        const pending = new Map();
        socket.addEventListener('message', event => {
            const message = JSON.parse(event.data);
            if (pending.has(message.id)) {
                const { resolve, reject } = pending.get(message.id);
                pending.delete(message.id);
                message.error ? reject(new Error(message.error.message)) : resolve(message.result);
            }
        });
        const send = (method, params = {}) => new Promise((resolve, reject) => {
            const id = ++sequence; pending.set(id, { resolve, reject });
            socket.send(JSON.stringify({ id, method, params }));
        });
        const evaluate = async expression => {
            const result = await send('Runtime.evaluate', { expression, returnByValue: true, awaitPromise: true });
            if (result.exceptionDetails) throw new Error(result.exceptionDetails.exception?.description || result.exceptionDetails.text);
            return result.result.value;
        };
        const click = async selector => {
            await send('Emulation.setFocusEmulationEnabled', { enabled: true });
            await send('Page.bringToFront');
            await evaluate(`document.querySelector(${JSON.stringify(selector)}).scrollIntoView({block:'center'})`);
            const point = await evaluate(`(() => { const rect = document.querySelector(${JSON.stringify(selector)}).getBoundingClientRect(); return {x:rect.x + rect.width/2, y:rect.y + rect.height/2}; })()`);
            await send('Input.dispatchMouseEvent', { type: 'mousePressed', ...point, button: 'left', clickCount: 1 });
            await send('Input.dispatchMouseEvent', { type: 'mouseReleased', ...point, button: 'left', clickCount: 1 });
        };
        await send('Page.enable');
        await send('Network.enable');
        if (process.argv.includes('--verification')) {
            await send('Page.navigate', { url: base + '/register' });
            await until(() => evaluate(`document.readyState==='complete' && !!document.querySelector('[name=password_confirmation]')`), 'Resume unverified registration');
            await evaluate(`document.querySelector('[name=name]').value='Verification Customer'; document.querySelector('[name=email]').value='verification-customer@logout-browser.test'; document.querySelector('[name=phone]').value='09123456789'; document.querySelector('[name=address]').value='Test Address'; document.querySelector('[name=password]').value='Browser-test-password-123'; document.querySelector('[name=password_confirmation]').value='Browser-test-password-123'`);
            await click('form button[type=submit]');
            await until(() => evaluate(`location.pathname==='/email/verify' && document.readyState==='complete'`), 'Email Verification page');
            console.log('Registration: repeat Sign Up resumes the unverified account and opens Email Verification PASSED');
            for (const theme of ['light', 'dark']) {
                await evaluate(`document.cookie='lavea_theme=${theme}; path=/'`);
                for (const width of [1280, 390, 320]) {
                    await send('Emulation.setDeviceMetricsOverride', { width, height: 900, deviceScaleFactor: 1, mobile: false });
                    await send('Page.reload');
                    await until(() => evaluate(`document.readyState==='complete' && document.documentElement.dataset.laveaTheme==='${theme}'`), 'Verification theme');
                    assert(await evaluate(`document.documentElement.scrollWidth<=innerWidth+1`), `Verification overflow at ${width}px`);
                    assert(await evaluate(`document.querySelector('.card').getBoundingClientRect().width<=440`), 'Compact verification card');
                    assert(await evaluate(`['.verify-btn','.resend-btn','.logout'].every(selector => { const button=document.querySelector(selector); const style=getComputedStyle(button); return button.getBoundingClientRect().height>=44 && style.color!==style.backgroundColor; })`), 'Visible verification controls');
                    await evaluate(`(() => { const input=document.querySelector('.code-input'); input.value='12ab345678'; input.dispatchEvent(new Event('input',{bubbles:true})); })()`);
                    assert.equal(await evaluate(`document.querySelector('.code-input').value`), '123456');
                    console.log(`Verification ${theme} ${width}px: layout, controls and code input PASSED`);
                }
            }
            await evaluate(`document.querySelector('.code-input').value='999999'`);
            await click('.verify-btn');
            await until(() => evaluate(`document.readyState==='complete' && !!document.getElementById('verification-errors')`), 'Incorrect verification feedback');
            assert(await evaluate(`document.getElementById('verification-errors').innerText.includes('incorrect')`));
            await click('.logout');
            await until(() => evaluate(`document.getElementById('logout-dialog').open`), 'Sign Out dialog');
            await evaluate(`document.getElementById('logout-dialog').close()`);
            await evaluate(`document.querySelector('.code-input').value='123456'`);
            await click('.verify-btn');
            await until(() => evaluate(`location.pathname==='/customer/dashboard' && document.readyState==='complete'`), 'Verified customer dashboard');
            console.log('Verification: incorrect code feedback, Sign Out dialog and successful verification PASSED');
            return;
        }
        if (process.argv.includes('--fulfillment')) {
            await send('Page.navigate', { url: base + '/login' });
            await until(() => evaluate(`document.readyState==='complete' && !!document.querySelector('[name=password]')`), 'Customer login');
            await evaluate(`document.querySelector('[name=email]').value='customer@logout-browser.test'; document.querySelector('[name=password]').value='Browser-test-password-123'; document.querySelector('[name=password]').form.requestSubmit()`);
            await until(() => evaluate(`location.pathname==='/customer/dashboard' && document.readyState==='complete'`), 'Customer dashboard');
            for (const theme of ['light','dark']) {
                await evaluate(`document.cookie='lavea_theme=${theme}; path=/'`);
                for (const width of [1280,390,320]) {
                    await send('Emulation.setDeviceMetricsOverride', { width, height:900, deviceScaleFactor:1, mobile:false });
                    await send('Page.navigate', { url:base+'/customer/services/1' });
                    await until(() => evaluate(`document.readyState==='complete' && !!document.getElementById('fulfillment_method') && document.documentElement.dataset.laveaTheme==='${theme}'`), 'Order fulfillment form');
                    assert(await evaluate(`document.getElementById('pickup_date').value==='' && !document.getElementById('pickup_date').disabled && document.getElementById('delivery_date').disabled && document.querySelector('[data-fulfillment=Delivery]').hidden`), 'Pickup starts blank and hides Delivery');
                    await evaluate(`document.getElementById('fulfillment_method').value='Delivery'; document.getElementById('fulfillment_method').dispatchEvent(new Event('change'))`);
                    assert(await evaluate(`document.getElementById('pickup_date').disabled && !document.getElementById('delivery_date').disabled && document.getElementById('delivery_address').required && !document.querySelector('[data-fulfillment=Delivery]').hidden`), 'Delivery shows required date and address');
                    assert(await evaluate(`document.documentElement.scrollWidth<=innerWidth+1`), 'Fulfillment form responsive');
                    console.log(`Fulfillment ${theme} ${width}px: Pickup/Delivery visibility and required fields PASSED`);
                }
            }
            await evaluate(`document.getElementById('quantity').value='2'; document.getElementById('delivery_date').value=document.getElementById('delivery_date').min; document.getElementById('delivery_address').value='Browser delivery address'`);
            await click('.customer-service-order form button[type=submit]');
            await until(() => evaluate(`document.readyState==='complete' && document.body.innerText.includes('Your order was placed successfully.')`), 'Delivery order saved').catch(async error => {
                console.log(await evaluate(`({url:location.href,text:document.body.innerText,valid:document.getElementById('quantity')?.form.checkValidity()})`));
                throw error;
            });
            await send('Page.navigate', { url:base+'/customer/orders/2' });
            await until(() => evaluate(`document.readyState==='complete' && document.body.innerText.includes('Browser delivery address')`), 'Saved Delivery details');
            assert(await evaluate(`document.body.innerText.includes('Browser Delivery Rider')`), 'Automatic delivery rider saved and displayed');
            await send('Page.navigate', { url:base+'/customer/orders/2/edit' });
            await until(() => evaluate(`document.readyState==='complete' && document.getElementById('fulfillment_method')?.value==='Delivery'`), 'Edit Delivery order');
            assert.equal(await evaluate(`document.getElementById('delivery_address').value`), 'Browser delivery address');
            await evaluate(`document.getElementById('fulfillment_method').value='Pickup'; document.getElementById('fulfillment_method').dispatchEvent(new Event('change')); document.getElementById('pickup_date').value=document.getElementById('pickup_date').min`);
            await click('.card.form-grid button[type=submit]');
            await until(() => evaluate(`location.pathname==='/customer/orders' && document.readyState==='complete' && document.body.innerText.includes('Your order was updated successfully.')`), 'Pickup change saved');
            await send('Page.navigate', { url:base+'/customer/orders/2' });
            await until(() => evaluate(`location.pathname==='/customer/orders/2' && document.readyState==='complete'`), 'Updated Pickup details');
            assert(await evaluate(`document.body.innerText.includes('Pickup') && !document.body.innerText.includes('Browser delivery address') && !document.body.innerText.includes('Browser Delivery Rider')`), 'Previous delivery address and rider cleared');
            console.log('Fulfillment: create Delivery, reopen saved details and switch to Pickup PASSED');
            return;
        }
        if (process.argv.includes('--details')) {
            for (const role of ['admin','manager']) {
                await send('Network.clearBrowserCookies');
                await send('Page.navigate', { url:base+'/login' });
                await until(() => evaluate(`document.readyState==='complete' && !!document.querySelector('[name=password]')`), 'Details login');
                await evaluate(`document.querySelector('[name=email]').value='${role}@logout-browser.test'; document.querySelector('[name=password]').value='Browser-test-password-123'; document.querySelector('[name=password]').form.requestSubmit()`);
                await until(() => evaluate(`location.pathname==='/${role}/dashboard' && document.readyState==='complete'`), role+' login');
                for (const theme of ['light','dark']) {
                    await evaluate(`document.cookie='lavea_theme=${theme}; path=/'`);
                    for (const width of [1280,390,320]) {
                        await send('Emulation.setDeviceMetricsOverride', { width, height:900, deviceScaleFactor:1, mobile:false });
                        for (const resource of ['customers','staff']) {
                            await send('Page.navigate', { url:base+`/${role}/${resource}/1` });
                            await until(() => evaluate(`location.pathname==='/${role}/${resource}/1' && document.readyState==='complete' && document.documentElement.dataset.laveaTheme==='${theme}'`), 'Details view');
                            assert(await evaluate(`!!document.querySelector('.details') && !!document.querySelector('.avatar')`), 'Shared detail design');
                            assert(await evaluate(`document.querySelector('.lavea-back-link').getAttribute('href').endsWith('/${role}/${resource}')`), 'Role appropriate Back link');
                            assert(await evaluate(`document.documentElement.scrollWidth<=innerWidth+1`), 'Details responsive');
                            if (role==='manager' && resource==='customers') {
                                assert(await evaluate(`document.body.innerText.includes('Order History') && !!document.querySelector('a[href$="/manager/customers/1/edit"]') && !!document.querySelector('a[href*="/manager/orders/create?customer_id=1"]')`), 'Manager features retained');
                            }
                            console.log(`${role} ${resource} details ${theme} ${width}px PASSED`);
                        }
                    }
                }
            }
            return;
        }
        for (const width of [1280, 390, 320]) {
            await send('Emulation.setDeviceMetricsOverride', { width, height: 900, deviceScaleFactor: 1, mobile: false });
            for (const [selector, destination] of [['.signin-btn', '/login'], ['.signup-btn', '/register']]) {
                await send('Page.navigate', { url: base + '/' });
                await until(() => evaluate(`location.pathname === '/' && document.readyState === 'complete'`), 'landing page');
                const point = await evaluate(`(() => { const link = document.querySelector(${JSON.stringify(selector)}); const rectangle = link.getBoundingClientRect(); return { x: rectangle.x + rectangle.width / 2, y: rectangle.y + rectangle.height / 2, width: rectangle.width, height: rectangle.height }; })()`);
                assert(point.width > 0 && point.height > 0 && point.x < width);
                await send('Input.dispatchMouseEvent', { type: 'mousePressed', x: point.x, y: point.y, button: 'left', clickCount: 1 });
                await send('Input.dispatchMouseEvent', { type: 'mouseReleased', x: point.x, y: point.y, button: 'left', clickCount: 1 });
                await until(() => evaluate(`location.pathname === ${JSON.stringify(destination)} && !!document.querySelector('[name=password]')`), destination + ' link');
                assert(await evaluate(`document.documentElement.scrollWidth <= innerWidth + 1`), `${destination} overflows at ${width}px`);
                console.log(`Landing ${width}px: ${destination} mouse click PASSED`);
            }
        }
        await send('Emulation.clearDeviceMetricsOverride');
        for (const theme of ['light', 'dark']) {
            await evaluate(`document.cookie = 'lavea_theme=${theme}; path=/'`);
            for (const width of [1280, 390, 320]) {
                await send('Emulation.setDeviceMetricsOverride', { width, height: 900, deviceScaleFactor: 1, mobile: false });
                await send('Page.navigate', { url: base + '/login' });
                await until(() => evaluate(`location.pathname === '/login' && document.readyState === 'complete' && document.documentElement.dataset.laveaTheme === '${theme}'`), 'Sign In theme');
                assert(await evaluate(`(() => {
                    const header = document.querySelector('.login-brand-header');
                    const back = document.querySelector('.login-back');
                    const brand = header.firstElementChild.getBoundingClientRect();
                    const bounds = back.getBoundingClientRect();
                    return bounds.left >= brand.right && bounds.right <= header.getBoundingClientRect().right && document.documentElement.scrollWidth <= innerWidth && getComputedStyle(back).backgroundColor === 'rgba(0, 0, 0, 0)' && getComputedStyle(back).color === 'rgb(255, 255, 255)' && getComputedStyle(header).backgroundColor === 'rgb(17, 29, 56)';
                })()`), `Sign In ${theme} header fits ${width}px`);
                assert.equal(await evaluate(`document.getElementById('password').getBoundingClientRect().height`), 46);
                await evaluate(`document.getElementById('password').value = 'Visibility-test-123'`);
                await click('.password-toggle');
                assert.equal(await evaluate(`document.getElementById('password').type`), 'text');
                assert.equal(await evaluate(`document.querySelector('.password-toggle').getAttribute('aria-label')`), 'Hide password');
                await click('.password-toggle');
                assert.equal(await evaluate(`document.getElementById('password').type`), 'password');
                assert.equal(await evaluate(`document.getElementById('password').value`), 'Visibility-test-123');
                await click('.remember input');
                assert.equal(await evaluate(`document.querySelector('[name=remember]').checked`), true);
                assert.equal(await evaluate(`location.pathname`), '/login');
                assert(await evaluate(`(() => {
                    const row = document.querySelector('.login-options').getBoundingClientRect();
                    return [...document.querySelector('.login-options').children].every(element => { const r = element.getBoundingClientRect(); return r.left >= row.left && r.right <= row.right && r.bottom <= row.bottom; });
                })()`), 'Remember me and password reset fit their row');
                await click('.login-options a');
                await until(() => evaluate(`location.pathname === '/forgot-password' && document.readyState === 'complete'`), 'password reset link');
                await send('Page.navigate', { url: base + '/login' });
                await until(() => evaluate(`location.pathname === '/login' && document.readyState === 'complete'`), 'Sign In return');
                await click('.signup-link a');
                await until(() => evaluate(`location.pathname === '/register' && document.readyState === 'complete'`), 'Sign Up link');
                await send('Page.navigate', { url: base + '/login' });
                await until(() => evaluate(`location.pathname === '/login' && document.readyState === 'complete'`), 'Sign In Back check');
                await click('.login-back');
                await until(() => evaluate(`location.pathname === '/' && document.readyState === 'complete'`), 'Back to home');
                console.log(`Sign In ${theme} ${width}px: layout, password eye, Remember me and navigation PASSED`);
            }
        }
        await send('Emulation.clearDeviceMetricsOverride');
        const authAppearance = async () => evaluate(`(() => {
            document.activeElement?.blur();
            const fields = {card:['.card',['width','borderRadius','backgroundColor','boxShadow']], header:['.login-brand-header',['padding','backgroundColor']], logo:['header strong',['fontSize','fontFamily','letterSpacing','color']], subtitle:['header span',['fontSize','color']], back:['.login-back',['padding','color','backgroundColor','border','borderRadius','fontSize']], input:['input[name=email]',['height','padding','border','borderRadius','fontFamily','fontSize','color','backgroundColor']], button:['.sign-in-button',['height','padding','borderRadius','backgroundColor','color','fontSize','fontWeight']]};
            return Object.fromEntries(Object.entries(fields).map(([name,[selector,properties]])=>{const styles=getComputedStyle(document.querySelector(selector));return [name,Object.fromEntries(properties.map(property=>[property,styles[property]]))]}));
        })()`);
        for (const theme of ['light','dark']) {
            await evaluate(`document.cookie='lavea_theme=${theme}; path=/'`);
            for (const width of [1280,600,520,390,320]) {
                await send('Emulation.setDeviceMetricsOverride',{width,height:900,deviceScaleFactor:1,mobile:false});
                await send('Page.navigate',{url:base+'/login'});
                await until(()=>evaluate(`location.pathname==='/login' && document.readyState==='complete'`),'Login appearance reference');
                const reference = await authAppearance();
                await send('Page.navigate',{url:base+'/register'});
                await until(()=>evaluate(`location.pathname==='/register' && document.readyState==='complete'`),'compact Register');
                assert.deepEqual(await authAppearance(),reference,`Register matches Login appearance in ${theme} at ${width}px`);
                assert(await evaluate(`document.documentElement.scrollWidth<=innerWidth+1 && (()=>{const back=document.querySelector('.login-back').getBoundingClientRect();return back.left>=document.querySelector('header strong').getBoundingClientRect().right})()`),'Register has no overflow or header overlap');
                assert(await evaluate(`[...document.querySelectorAll('input:not([type=hidden])')].every(input=>input.getBoundingClientRect().height===46)`),'Register inputs have Login height');
                for (const id of ['password','password_confirmation']) {
                    await evaluate(`document.getElementById('${id}').value='Register-test-123'`);
                    await click(`[data-password-target="${id}"]`);
                    assert.equal(await evaluate(`document.getElementById('${id}').type`),'text');
                    await click(`[data-password-target="${id}"]`);
                    assert.equal(await evaluate(`document.getElementById('${id}').type`),'password');
                    assert.equal(await evaluate(`document.getElementById('${id}').value`),'Register-test-123');
                }
                await click('.signup-link a');
                await until(()=>evaluate(`location.pathname==='/login' && document.readyState==='complete'`),'Register Sign In link');
                await send('Page.navigate',{url:base+'/register'});
                await until(()=>evaluate(`location.pathname==='/register' && document.readyState==='complete'`),'Register Back check');
                await click('.login-back');
                await until(()=>evaluate(`location.pathname==='/' && document.readyState==='complete'`),'Register Back to home');
                console.log(`Register ${theme} ${width}px: Login styling, fields, password visibility and links PASSED`);
            }
        }
        await send('Emulation.clearDeviceMetricsOverride');
        for (const role of ['admin', 'manager', 'customer']) {
            await send('Network.clearBrowserCookies');
            await send('Page.navigate', { url: base + '/login' });
            await until(() => evaluate('!!document.querySelector("input[name=password]")'), 'login form');
            await evaluate(`document.querySelector('[name=email]').value = ${JSON.stringify(role + '@logout-browser.test')}; document.querySelector('[name=password]').value = 'Browser-test-password-123'; document.querySelector('[name=password]').form.requestSubmit();`);
            await until(() => evaluate(`location.pathname === '/${role}/dashboard' && document.readyState === 'complete'`), role + ' dashboard');
            if (role === 'admin' && process.argv.includes('--reports')) {
                await send('Page.navigate', { url: base + '/admin/reports' });
                await until(() => evaluate(`location.pathname === '/admin/reports' && document.readyState === 'complete'`), 'Reports form');
                for (const reportType of ['payments', 'orders', 'services', 'all']) {
                    await evaluate(`document.getElementById('report_type').value='${reportType}'; document.getElementById('from_date').value='2026-10-10'; document.getElementById('to_date').value='2026-10-13'`);
                    await click('.filter-row button[type=submit]');
                    await until(() => evaluate(`new URLSearchParams(location.search).get('report_type')==='${reportType}' && document.readyState==='complete'`), reportType + ' report generated by click');
                    assert.equal(await evaluate(`document.getElementById('from_date').value`), '2026-10-10');
                    for (const section of ['orders', 'payments', 'services']) {
                        assert.equal(await evaluate(`!!document.getElementById('${section}-report')`), reportType === 'all' || reportType === section);
                    }
                    console.log(`Reports: Generate ${reportType} with historical dates PASSED`);
                }
                await evaluate(`document.getElementById('from_date').value=''; document.getElementById('to_date').value=''`);
                await click('.filter-row button[type=submit]');
                await until(() => evaluate(`new URLSearchParams(location.search).get('from_date')==='' && document.readyState==='complete'`), 'All-date report');
                assert(await evaluate(`document.getElementById('payments-report').innerText.includes('100.00')`), 'Confirmed income displayed');
                for (const theme of ['light', 'dark']) {
                    await evaluate(`document.cookie='lavea_theme=${theme}; path=/'`);
                    for (const width of [1280, 390, 320]) {
                        await send('Emulation.setDeviceMetricsOverride', { width, height: 900, deviceScaleFactor: 1, mobile: false });
                        await send('Page.reload');
                        await until(() => evaluate(`document.readyState==='complete' && document.documentElement.dataset.laveaTheme==='${theme}'`), 'Report theme');
                        assert(await evaluate(`document.documentElement.scrollWidth<=innerWidth+1`), `Reports ${theme} overflow at ${width}px`);
                        assert(await evaluate(`(() => { const button=document.querySelector('.filter-row button'); const style=getComputedStyle(button); return button.getBoundingClientRect().width>0 && style.color!==style.backgroundColor; })()`), 'Generate visible');
                        console.log(`Reports ${theme} ${width}px: form and tables responsive PASSED`);
                    }
                }
                break;
            }
            if (role === 'admin') {
                await send('Page.navigate', { url: base + '/admin/settings' });
                await until(() => evaluate(`document.readyState === 'complete' && !!document.getElementById('gcash_number')`), 'Admin GCash settings');
                await evaluate(`document.getElementById('gcash_number').value='09123456789'; document.getElementById('gcash_account_name').value='Browser Test Merchant'`);
                const qrDocument = await send('DOM.getDocument');
                const qrInput = await send('DOM.querySelector', { nodeId: qrDocument.root.nodeId, selector: '#gcash_qr' });
                await send('DOM.setFileInputFiles', { nodeId: qrInput.nodeId, files: [path.join(workspace, 'public/images/laundry-home.jpg')] });
                await evaluate(`document.getElementById('gcash_number').form.requestSubmit()`);
                await until(() => evaluate(`document.readyState === 'complete' && document.body.innerText.includes('GCash account settings saved.')`), 'saved GCash details');
                console.log('Admin: GCash account and optional QR saved PASSED');
                await send('Page.navigate', { url: base + '/admin/payments/2' });
                await until(() => evaluate(`document.readyState === 'complete' && !!document.getElementById('rejection_reason')`), 'Admin review form');
                await send('Emulation.setDeviceMetricsOverride', { width: 390, height: 900, deviceScaleFactor: 1, mobile: false });
                assert(await evaluate(`document.documentElement.scrollWidth <= innerWidth + 1`), 'Admin approval form fits mobile');
                await evaluate(`document.getElementById('rejection_reason').value = 'Image unclear'; document.getElementById('rejection_reason').form.requestSubmit()`);
                await until(() => evaluate(`document.readyState === 'complete' && document.body.innerText.includes('Image unclear') && !document.getElementById('rejection_reason')`), 'Admin rejection');
                await send('Page.navigate', { url: base + '/admin/payments/4' });
                await until(() => evaluate(`document.readyState === 'complete' && !!document.querySelector('input[name=status][value=Confirmed]')`), 'Admin confirmation form');
                await evaluate(`document.querySelector('input[name=status][value=Confirmed]').form.requestSubmit()`);
                await until(() => evaluate(`document.readyState === 'complete' && !document.querySelector('input[name=status][value=Confirmed]') && document.body.innerText.includes('Print Receipt')`), 'Admin confirmation');
                console.log('Admin: confirm and reject with a reason PASSED');
            } else if (role === 'manager') {
                await send('Page.navigate', { url: base + '/manager/payments/2/edit' });
                await until(() => evaluate(`document.readyState === 'complete' && !!document.getElementById('amount')`), 'Staff correction form');
                await evaluate(`document.getElementById('amount').form.requestSubmit()`);
                await until(() => evaluate(`location.pathname === '/manager/payments/2' && document.readyState === 'complete' && document.body.innerText.includes('Waiting for Admin approval')`), 'Staff resubmission');
                assert(await evaluate(`!!document.querySelector('img[alt^="Proof of payment"]') && !document.body.innerText.includes('Confirm Payment')`), 'existing Customer proof reused without Staff approval');
                await send('Page.navigate', { url: base + '/manager/payments/create' });
                await until(() => evaluate(`document.readyState === 'complete' && !!document.getElementById('proof_image')`), 'Staff upload form');
                await evaluate(`document.getElementById('order_id').value='1'; document.getElementById('amount').value='20'; document.getElementById('payment_method').value='GCash'; document.getElementById('reference_number').value='BROWSER-STAFF-UPLOAD'`);
                await evaluate(`document.getElementById('payment_method').dispatchEvent(new Event('change',{bubbles:true})); document.getElementById('amount').dispatchEvent(new Event('input',{bubbles:true}))`);
                assert(await evaluate(`!document.querySelector('[data-gcash-payment]').hidden && document.querySelector('[data-gcash-number]').textContent === '09123456789' && document.querySelector('[data-gcash-amount]').textContent.includes('20.00')`), 'Staff uses saved number and current amount');
                await send('Browser.grantPermissions', { origin: base, permissions: ['clipboardReadWrite', 'clipboardSanitizedWrite'] });
                await click('[data-copy-gcash]');
                await until(() => evaluate(`document.querySelector('[data-copy-status]').textContent === 'Number copied'`), 'Staff Copy Number');
                assert(await evaluate(`navigator.clipboard.readText().then(text => text === document.querySelector('[data-gcash-number]').textContent)`), 'correct Staff clipboard number');
                await until(() => evaluate(`document.querySelector('img[alt="Shop GCash QR code"]').naturalWidth > 0`), 'Staff saved QR image');
                const uploadDocument = await send('DOM.getDocument');
                const uploadInput = await send('DOM.querySelector', { nodeId: uploadDocument.root.nodeId, selector: '#proof_image' });
                await send('DOM.setFileInputFiles', { nodeId: uploadInput.nodeId, files: [path.join(workspace, 'public/images/laundry-home.jpg')] });
                await evaluate(`document.getElementById('amount').form.requestSubmit()`);
                try {
                    await until(() => evaluate(`location.pathname.startsWith('/manager/payments/') && location.pathname !== '/manager/payments/create' && document.readyState === 'complete' && document.body.innerText.includes('Waiting for Admin approval') && !!document.querySelector('img[alt^="Proof of payment"]')`), 'Staff proof upload');
                } catch (error) {
                    throw new Error('Staff proof upload: ' + await evaluate(`JSON.stringify({path:location.pathname, errors:document.querySelector('.error-list')?.innerText, invalid:Array.from(document.querySelectorAll('input:invalid,select:invalid')).map(input => input.name), body:document.body.innerText.slice(-1200)})`));
                }
                console.log('Staff: upload and resubmit using existing proof PASSED');
            } else {
                await send('Page.navigate', { url: base + '/customer/payments/orders/1' });
                await until(() => evaluate(`document.readyState === 'complete' && !!document.querySelector('[data-copy-gcash]')`), 'Customer GCash panel');
                await evaluate(`document.getElementById('amount').value='15'; document.getElementById('amount').dispatchEvent(new Event('input',{bubbles:true})); document.getElementById('reference_number').value='BROWSER-CUSTOMER-UPLOAD'`);
                assert(await evaluate(`document.querySelector('[data-gcash-amount]').textContent.includes('15.00') && document.body.innerText.includes('Browser Test Merchant')`), 'Customer uses saved name and selected amount');
                await send('Browser.grantPermissions', { origin: base, permissions: ['clipboardReadWrite', 'clipboardSanitizedWrite'] });
                await click('[data-copy-gcash]');
                await until(() => evaluate(`document.querySelector('[data-copy-status]').textContent === 'Number copied'`), 'Customer Copy Number');
                assert(await evaluate(`navigator.clipboard.readText().then(text => text === document.querySelector('[data-gcash-number]').textContent)`), 'correct Customer clipboard number');
                await until(() => evaluate(`document.querySelector('img[alt="Shop GCash QR code"]').naturalWidth > 0`), 'Customer saved QR image');
                const proofDocument = await send('DOM.getDocument');
                const proofInput = await send('DOM.querySelector', { nodeId: proofDocument.root.nodeId, selector: '#proof_image' });
                await send('DOM.setFileInputFiles', { nodeId: proofInput.nodeId, files: [path.join(workspace, 'public/images/laundry-home.jpg')] });
                await evaluate(`document.getElementById('amount').form.requestSubmit()`);
                await until(() => evaluate(`location.pathname.startsWith('/customer/payments/') && document.readyState === 'complete' && document.body.innerText.includes('Payment is pending Admin approval.')`), 'Customer proof submitted Pending');
                console.log('Staff and Customer: saved GCash details, QR, clipboard and Pending uploads PASSED');
                await send('Page.navigate', { url: base + '/customer/payments/3' });
                await until(() => evaluate(`document.readyState === 'complete' && document.body.innerText.includes('Browser rejected reason')`), 'Customer rejection reason');
                assert(await evaluate(`!!document.querySelector('img[alt^="Proof of payment"]') && !document.body.innerText.includes('Print Receipt')`), 'Customer rejected payment proof and status');
                console.log('Customer: existing proof and rejection reason PASSED');
            }
            if (role === 'manager') {
                await send('Page.navigate', { url: base + '/manager/orders/create' });
                await until(() => evaluate(`location.pathname === '/manager/orders/create' && document.readyState === 'complete' && !!document.getElementById('assigned_staff')`), 'Manager assignment form');
                await evaluate(`document.getElementById('customer_id').value='1'; document.getElementById('service_id').value='1'; document.getElementById('service_id').dispatchEvent(new Event('change'))`);
                await until(() => evaluate(`document.getElementById('assigned_staff').value==='Browser Idle Laundry Worker' && !document.querySelector('.actions button').disabled`), 'least unfinished Laundry worker suggested');
                assert(await evaluate(`document.getElementById('assigned_staff').readOnly && !document.querySelector('[name=staff_id]')`), 'automatic assignment shown without a manual staff control');
                await evaluate(`document.getElementById('service_id').value='2'; document.getElementById('service_id').dispatchEvent(new Event('change'))`);
                await until(() => evaluate(`document.getElementById('assigned_staff').value==='Browser Dry Worker'`), 'Dry Cleaning worker selected');
                await evaluate(`document.getElementById('service_id').value='3'; document.getElementById('service_id').dispatchEvent(new Event('change'))`);
                await until(() => evaluate(`document.getElementById('assigned_staff').value==='Browser Ironing Worker'`), 'Ironing worker selected');
                await evaluate(`document.getElementById('service_id').value='4'; document.getElementById('service_id').dispatchEvent(new Event('change'))`);
                await until(() => evaluate(`document.getElementById('assignment-message').textContent.includes('Configure the required job position') && document.querySelector('.actions button').disabled && !document.getElementById('assignment-config-link').hidden`), 'unmapped service blocked with configuration link');
                await click('#assignment-config-link');
                await until(() => evaluate(`location.pathname === '/manager/services/4' && document.readyState === 'complete'`), 'Manager mapping configuration');
                await evaluate(`document.getElementById('required_job_position').value='Ironing Attendant'; document.getElementById('required_job_position').form.requestSubmit()`);
                await until(() => evaluate(`document.readyState === 'complete' && document.body.innerText.includes('Required job position saved.')`), 'required job position saved');
                await send('Page.navigate', { url: base + '/manager/orders/create' });
                await until(() => evaluate(`location.pathname === '/manager/orders/create' && document.readyState === 'complete'`), 'assignment form with configured service');
                await evaluate(`document.getElementById('customer_id').value='1'; document.getElementById('service_id').value='4'; document.getElementById('service_id').dispatchEvent(new Event('change'))`);
                await until(() => evaluate(`document.getElementById('assigned_staff').value==='Browser Ironing Worker'`), 'configured service assigns Ironing worker');
                await evaluate(`document.getElementById('service_id').value='2'; document.getElementById('service_id').dispatchEvent(new Event('change'))`);
                await until(() => evaluate(`document.getElementById('assigned_staff').value==='Browser Dry Worker'`), 'Dry worker before availability change');
                assert(await evaluate(`fetch('/manager/staff/3',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:new URLSearchParams({_token:document.querySelector('input[name="_token"]').value,_method:'PUT',name:'Browser Dry Worker',role:'Dry Cleaning Attendant',status:'Inactive'})}).then(response=>response.ok)`), 'worker availability updated in isolated database');
                await click('#refresh-assignment');
                await until(() => evaluate(`document.getElementById('assignment-message').textContent.includes('No active Dry Cleaning Attendant') && document.querySelector('.actions button').disabled`), 'unavailable worker blocks save');
                await evaluate(`document.getElementById('service_id').value='1'; document.getElementById('service_id').dispatchEvent(new Event('change'))`);
                await until(() => evaluate(`document.getElementById('assigned_staff').value==='Browser Idle Laundry Worker'`), 'Laundry suggestion refreshed');
                assert.equal(await evaluate(`document.querySelector('.actions button').disabled`), false, 'automatic assignment available');
                assert(await evaluate(`document.documentElement.scrollWidth<=innerWidth+1`), 'assignment form fits mobile');
                await evaluate(`document.getElementById('assigned_staff').form.requestSubmit()`);
                await until(() => evaluate(`location.pathname==='/manager/orders' && document.readyState==='complete' && document.body.innerText.includes('Order added successfully.')`), 'automatic assignment saved');
                assert(await evaluate(`document.body.textContent.includes('Browser Laundry Worker') && document.body.textContent.includes('Assigned Staff')`), 'assignment visible in Manager list');
                await send('Page.navigate', { url: base + '/manager/orders/2/edit' });
                await until(() => evaluate(`document.readyState==='complete' && document.getElementById('assigned_staff')?.value==='Browser Idle Laundry Worker'`), 'existing automatic assignment preserved');
                await evaluate(`document.getElementById('service_id').value='3'; document.getElementById('service_id').dispatchEvent(new Event('change'))`);
                await until(() => evaluate(`document.getElementById('assigned_staff').value==='Browser Ironing Worker'`), 'service change recalculates assignment');
                await evaluate(`document.getElementById('status').value='Processing'; document.getElementById('status').dispatchEvent(new Event('change')); document.getElementById('assigned_staff').form.requestSubmit()`);
                await until(() => evaluate(`location.pathname==='/manager/orders/2' && document.readyState==='complete' && document.body.innerText.includes('Browser Ironing Worker')`), 'assignment and progress saved');
                await send('Page.navigate', { url: base + '/manager/orders/2/edit' });
                await until(() => evaluate(`document.readyState==='complete' && document.getElementById('service_id')?.disabled && document.getElementById('assigned_staff')?.readOnly`), 'started order assignment locked');
                assert.equal(await evaluate(`document.getElementById('assigned_staff').value`), 'Browser Ironing Worker');
                console.log('Manager: service mapping, least workload, automatic assignment, unavailable staff and progress lock PASSED');
                await send('Page.navigate', { url: base + '/manager/staff/create' });
                await until(() => evaluate(`document.readyState==='complete' && !!document.getElementById('custom_position')`), 'staff custom form');
                assert.equal(await evaluate(`!!document.querySelector('[name=email]')`), false);
                await evaluate(`document.getElementById('role').value='Other'; document.getElementById('role').dispatchEvent(new Event('change')); document.getElementById('name').value='Custom Worker'`);
                assert(await evaluate(`!document.getElementById('custom-position-field').hidden && document.getElementById('custom_position').required && !document.getElementById('custom_position').checkValidity()`), 'Other shows a required custom input');
                await evaluate(`document.getElementById('custom_position').value='Quality Checker'; document.getElementById('custom_position').form.requestSubmit()`);
                await until(() => evaluate(`location.pathname==='/manager/staff' && document.readyState==='complete' && document.body.textContent.includes('Quality Checker')`), 'custom worker saved without email');
                await send('Emulation.setDeviceMetricsOverride', { width:320,height:900,deviceScaleFactor:1,mobile:false });
                await evaluate(`document.cookie='lavea_theme=dark; path=/'`);
                await send('Page.navigate', { url: base + '/manager/staff/5/edit' });
                await until(() => evaluate(`document.readyState==='complete' && document.getElementById('custom_position')?.value==='Quality Checker'`), 'custom position prefilled in Edit Staff');
                assert.equal(await evaluate(`document.getElementById('role').value`), 'Other');
                assert(await evaluate(`document.documentElement.scrollWidth<=innerWidth+1 && !document.querySelector('[name=email]')`), 'custom form fits mobile dark mode without email');
                await evaluate(`document.getElementById('role').value='Laundry Attendant'; document.getElementById('role').dispatchEvent(new Event('change'))`);
                assert(await evaluate(`document.getElementById('custom-position-field').hidden && document.getElementById('custom_position').disabled && !document.getElementById('custom_position').required`), 'standard position hides custom input');
                await evaluate(`document.getElementById('role').value='Other'; document.getElementById('role').dispatchEvent(new Event('change')); document.getElementById('custom_position').value='Packing Assistant'; document.getElementById('custom_position').form.requestSubmit()`);
                await until(() => evaluate(`location.pathname==='/manager/staff' && document.readyState==='complete' && document.body.textContent.includes('Packing Assistant')`), 'custom position update saved');
                await evaluate(`document.cookie='lavea_theme=light; path=/'`);
                console.log('Staff forms: no email, Other validation, custom create/edit, mobile and dark mode PASSED');
            }
            await send('Emulation.clearDeviceMetricsOverride');
            await send('Page.navigate', { url: base + `/${role}/dashboard` });
            await until(() => evaluate(`location.pathname === '/${role}/dashboard' && document.readyState === 'complete'`), 'dashboard after payment checks');
            const checkSidebarBranding = async () => {
                assert(await evaluate(`!document.querySelector('.lavea-topbar-logo') && !document.querySelector('.lavea-admin-topbar .logo') && document.querySelector('.sidebar .logo').textContent==='LAVEA'`), 'branding is only in the sidebar');
                if (role === 'admin') {
                    assert.equal(await evaluate(`!!document.querySelector('.sidebar a[href$="/admin/managers"]')`),false,'Manager Accounts removed from Admin sidebar');
                }
                const collapsed = await evaluate(`innerWidth>900 && document.body.classList.contains('lavea-sidebar-collapsed')`);
                if (await evaluate(`innerWidth>900`)) {
                    await until(()=>evaluate(`Math.abs(document.querySelector('.sidebar').getBoundingClientRect().width-${collapsed ? 76 : 255})<1`),'sidebar width transition');
                    assert.equal(await evaluate(`document.querySelector('.sidebar').inert`),false,'desktop icon rail remains interactive');
                }
                assert(await evaluate(`[...document.querySelectorAll('.sidebar .menu a')].every(link=> {
                    const label=link.querySelector('span');
                    const collapsed=innerWidth>900 && document.body.classList.contains('lavea-sidebar-collapsed');
                    return !!link.getAttribute('aria-label') && (collapsed ? getComputedStyle(label).display==='none' && link.title===label.textContent.trim() : getComputedStyle(label).display!=='none' && !link.hasAttribute('title'));
                })`),`${role} labels and collapsed name tooltips`);
                assert(await evaluate(`document.documentElement.scrollWidth<=innerWidth+1`),'sidebar layout fits viewport');
            };
            for (const width of [1280, 1024, 900, 820, 600, 390, 320]) {
                await send('Emulation.setDeviceMetricsOverride', { width, height: 900, deviceScaleFactor: 1, mobile: false });
                await pause(250);
                assert.equal(await evaluate(`document.querySelector('.lavea-sidebar-toggle').getAttribute('aria-expanded')`), width > 900 ? 'true' : 'false');
                await checkSidebarBranding(width <= 900);
                await evaluate(`document.querySelector('.lavea-sidebar-toggle').click()`);
                await pause(250);
                await checkSidebarBranding(true);
                if (width > 900) {
                    await until(()=>evaluate(`Math.abs(parseFloat(getComputedStyle(document.querySelector('.main')).marginLeft)-76)<0.1`),'main content follows collapsed rail');
                    assert.equal(await evaluate(`document.querySelector('.sidebar').inert`), false);
                    await evaluate(`document.querySelector('.lavea-sidebar-toggle').click()`);
                    await pause(250);
                    await until(()=>evaluate(`Math.abs(parseFloat(getComputedStyle(document.querySelector('.main')).marginLeft)-255)<0.1`),'main content follows expanded sidebar');
                    await checkSidebarBranding(false);
                } else {
                    assert.equal(await evaluate(`document.body.classList.contains('lavea-sidebar-open')`), true);
                    assert.equal(await evaluate(`getComputedStyle(document.querySelector('.lavea-sidebar-backdrop')).opacity`), '1');
                    await evaluate(`document.querySelector('.lavea-sidebar-backdrop').click()`);
                    assert.equal(await evaluate(`document.querySelector('.sidebar').inert`), true);
                    await evaluate(`document.querySelector('.lavea-sidebar-toggle').click(); document.dispatchEvent(new KeyboardEvent('keydown', {key:'Escape'}))`);
                    assert.equal(await evaluate(`document.querySelector('.lavea-sidebar-toggle').getAttribute('aria-expanded')`), 'false');
                    await evaluate(`document.querySelector('.lavea-sidebar-toggle').click(); document.querySelector('.sidebar a').click()`);
                    await until(() => evaluate(`document.readyState === 'complete' && !document.body.classList.contains('lavea-sidebar-open')`), 'menu link closes sidebar');
                }
                console.log(`${role} sidebar toggle ${width}px PASSED`);
            }
            await send('Emulation.clearDeviceMetricsOverride');
            await evaluate(`document.cookie = 'lavea_theme=dark; path=/'; location.reload()`);
            await until(() => evaluate(`document.documentElement.dataset.laveaTheme === 'dark' && document.readyState === 'complete'`), 'dark dashboard');
            assert.equal(await evaluate(`getComputedStyle(document.querySelector('.lavea-sidebar-toggle')).color`), 'rgb(231, 237, 249)');
            assert.equal(await evaluate(`getComputedStyle(document.querySelector('.lavea-sidebar-toggle')).backgroundColor`), 'rgb(39, 52, 73)');
            for (const width of [1280, 820, 390, 320]) {
                await send('Emulation.setDeviceMetricsOverride', { width, height: 900, deviceScaleFactor: 1, mobile: false });
                await pause(250);
                await checkSidebarBranding(width <= 900);
                await evaluate(`document.querySelector('.lavea-sidebar-toggle').click()`);
                await pause(250);
                await checkSidebarBranding(true);
                await evaluate(`document.querySelector('.lavea-sidebar-toggle').click()`);
                await pause(250);
                await checkSidebarBranding(width <= 900);
                console.log(`${role} dark sidebar and logo ${width}px PASSED`);
            }
            await send('Emulation.clearDeviceMetricsOverride');
            if (role === 'admin') {
                await send('Page.navigate', { url: base + '/admin/services/1/edit' });
                await until(() => evaluate(`location.pathname === '/admin/services/1/edit' && document.readyState === 'complete'`), 'service edit form');
                const document = await send('DOM.getDocument');
                const input = await send('DOM.querySelector', { nodeId: document.root.nodeId, selector: '#image' });
                await send('DOM.setFileInputFiles', { nodeId: input.nodeId, files: [path.join(workspace, 'public/images/laundry-home.jpg')] });
                await until(() => evaluate(`!document.getElementById('service-image-preview').hidden`), 'selected image preview');
                await evaluate(`document.getElementById('image').form.requestSubmit()`);
                await until(() => evaluate(`location.pathname === '/admin/services' && document.readyState === 'complete'`), 'service image saved');
                const imagePath = await evaluate(`new URL(document.querySelector('img.service-image').src).pathname`);
                assert(/^\/storage\/services\/[a-zA-Z0-9_-]+\.(jpg|jpeg|png|webp)$/i.test(imagePath));
                uploadedServiceImages.push(path.join(workspace, 'storage/app/public', imagePath.slice('/storage/'.length)));
                assert(await evaluate(`(() => { const image = document.querySelector('img.service-image'); return image.complete && image.naturalWidth > 0; })()`), 'saved service image cannot be displayed');
                console.log('Admin: select image → submit edit → save → display image PASSED');
                await send('Page.navigate', { url: base + '/admin/dashboard' });
                await until(() => evaluate(`location.pathname === '/admin/dashboard' && document.readyState === 'complete'`), 'admin dashboard');
            }
            await send('Page.navigate', { url: base + '/payments/1/receipt' });
            await until(() => evaluate(`location.pathname === '/payments/1/receipt' && document.readyState === 'complete'`), role + ' receipt page');
            assert(await evaluate(`!!document.getElementById('payment-receipt')`), role + ' receipt response: ' + await evaluate(`document.body.innerText.slice(0, 600)`));
            await send('Emulation.setEmulatedMedia', { media: 'print' });
            assert(await evaluate(`['.sidebar', '.lavea-admin-topbar', '.lavea-sidebar-backdrop'].every(selector => getComputedStyle(document.querySelector(selector)).display === 'none')`), 'navigation hidden when printing');
            assert(await evaluate(`(() => { const receipt = document.getElementById('payment-receipt'); return getComputedStyle(receipt).display === 'block' && receipt.scrollWidth <= receipt.clientWidth + 1 && getComputedStyle(receipt).color === 'rgb(23, 37, 84)'; })()`), role + ' receipt fits and retains colors');
            const pdf = await send('Page.printToPDF', { preferCSSPageSize: true, printBackground: true, displayHeaderFooter: false });
            assert.match(Buffer.from(pdf.data, 'base64').toString('latin1'), /\/Count 1\b/, role + ' receipt fits one A4 page');
            await send('Emulation.setEmulatedMedia', { media: '' });
            console.log(`${role}: original receipt prints on one A4 page PASSED`);
            const responsivePages = role === 'admin'
                ? ['dashboard', 'customers', 'customers/1', 'services', 'services/1', 'services/1/edit', 'staff', 'staff/1', 'orders', 'orders/1', 'payments', 'payments/1', 'settings', 'profile', 'services/create', 'managers', 'managers/create', 'managers/2/edit', 'staff/create', 'schedules', 'schedules/1', 'schedules/1/edit', 'schedules/create', 'reports']
                : role === 'manager'
                    ? ['dashboard', 'customers', 'customers/1', 'customers/1/edit', 'services', 'services/1', 'orders', 'orders/1', 'orders/1/edit', 'payments', 'payments/1', 'payments/create', 'payments/3/edit', 'settings', 'profile', 'orders/create', 'customers/create', 'staff', 'staff/1', 'staff/create', 'staff/1/edit', 'schedules', 'schedules/create', 'schedules/1', 'schedules/1/edit']
                    : ['dashboard', 'services', 'services/1', 'orders', 'orders/1', 'payments', 'payments/1', 'payments/orders/1', 'settings', 'profile'];
            for (const width of (process.env.NAVIGATION_ONLY === '1' || process.argv.includes('--receipts') ? [] : [320, 360, 390, 600, 768, 820, 1024, 1280])) {
                await send('Emulation.setDeviceMetricsOverride', { width, height: 900, deviceScaleFactor: 1, mobile: false });
                for (const page of responsivePages) {
                    const pathname = `/${role}/${page}`;
                    await send('Page.navigate', { url: base + pathname });
                    await until(() => evaluate(`location.pathname === ${JSON.stringify(pathname)} && document.readyState === 'complete'`), pathname);
                    if (role === 'manager' && page === 'payments/create') {
                        await evaluate(`document.getElementById('payment_method').value='GCash'; document.getElementById('payment_method').dispatchEvent(new Event('change',{bubbles:true})); document.getElementById('amount').value='20'; document.getElementById('amount').dispatchEvent(new Event('input',{bubbles:true}))`);
                        assert(await evaluate(`!document.querySelector('[data-gcash-payment]').hidden`), 'Staff GCash panel visible in responsive check');
                    }
                    const dimensions = await evaluate(`({ viewport: innerWidth, page: document.documentElement.scrollWidth })`);
                    assert(await evaluate(`!!document.querySelector('.dashboard')`), `${pathname} did not render its layout`);
                    assert(dimensions.page <= dimensions.viewport + 1, `${pathname} overflows at ${width}px: ${dimensions.page}px`);
                }
                console.log(`${role} responsive pages ${width}px PASSED`);
                await evaluate(`document.querySelector('form[data-confirm-logout]').requestSubmit()`);
                const modalFits = await evaluate(`(() => { const dialog = document.getElementById('logout-dialog'); const bounds = dialog.getBoundingClientRect(); return dialog.open && bounds.left >= 0 && bounds.right <= innerWidth && bounds.top >= 0 && bounds.bottom <= innerHeight; })()`);
                assert(modalFits, `${role} logout popup does not fit at ${width}px`);
                await evaluate(`document.querySelector('[data-logout-cancel]').click()`);
            }
            await send('Emulation.clearDeviceMetricsOverride');
            await send('Page.navigate', { url: base + `/${role}/dashboard` });
            await until(() => evaluate(`location.pathname === '/${role}/dashboard' && document.readyState === 'complete'`), role + ' dashboard');
            await send('Page.navigate', { url: base + `/${role}/services` });
            await until(() => evaluate(`location.pathname === '/${role}/services' && document.readyState === 'complete'`), role + ' services');
            await send('Page.navigate', { url: base + `/${role}/orders` });
            await until(() => evaluate(`location.pathname === '/${role}/orders' && document.readyState === 'complete'`), role + ' records');
            await evaluate('history.back()');
            await until(() => evaluate(`location.pathname === '/${role}/dashboard' && document.readyState === 'complete'`), role + ' Back returns to dashboard');
            console.log(`${role}: browser Back returns to dashboard PASSED`);
            const history = await send('Page.getNavigationHistory');
            const dashboardEntry = history.entries[history.currentIndex];
            const headers = await evaluate(`fetch(location.href, {cache:'no-store'}).then(r => ({cache:r.headers.get('cache-control'), pragma:r.headers.get('pragma'), expires:r.headers.get('expires')}))`);
            assert(headers.cache.includes('no-store') && headers.cache.includes('no-cache') && headers.cache.includes('must-revalidate') && headers.cache.includes('max-age=0'));
            assert.equal(headers.pragma, 'no-cache'); assert.equal(headers.expires, '0');
            await evaluate(`document.querySelector('form[action$="/logout"]').requestSubmit()`);
            await until(() => evaluate(`document.getElementById('logout-dialog').open`), 'logout dialog');
            await evaluate(`document.querySelector('[data-logout-cancel]').click()`);
            assert.equal(await evaluate(`document.getElementById('logout-dialog').open`), false);
            await evaluate(`document.querySelector('form[action$="/logout"]').requestSubmit()`);
            await evaluate(`document.querySelector('[data-logout-confirm]').click()`);
            await until(() => evaluate(`location.pathname === '/'`), 'logout landing page redirect');
            await evaluate('history.back()');
            await pause(600);
            await until(() => evaluate(`location.pathname === '/login' && !!document.querySelector('[name=password]')`), 'Back after logout');
            await pause(300);
            await send('Page.navigateToHistoryEntry', { entryId: dashboardEntry.id });
            await pause(600);
            await until(() => evaluate(`location.pathname === '/login' && !!document.querySelector('[name=password]')`), 'Back to dashboard');
            assert.equal(await evaluate(`!!document.querySelector('.dashboard')`), false);
            console.log(role + ': Login → Dashboard → Sign Out → Back → login PASSED');
        }
    } finally {
        for (const imagePath of uploadedServiceImages) {
            const serviceRoot = path.resolve(workspace, 'storage/app/public/services') + path.sep;
            if (path.resolve(imagePath).startsWith(serviceRoot) && fs.existsSync(imagePath)) fs.unlinkSync(imagePath);
        }
        socket?.close();
        for (const process of [chrome, server]) {
            if (process) {
                try {
                    execFileSync('taskkill', ['/PID', String(process.pid), '/T', '/F'], { stdio: 'ignore' });
                } catch {}
            }
        }
        execFileSync(phpPath, ['tests/Feature/LogoutBrowserFixture.php'], { cwd: workspace, env: { ...environment, LAVEA_BROWSER_CLEANUP: '1' }, stdio: 'pipe' });
        const resolved = path.resolve(temporary);
        if (resolved.startsWith(path.resolve(os.tmpdir()) + path.sep) && path.basename(resolved).startsWith('lavea-logout-browser-')) fs.rmSync(resolved, { recursive: true, force: true, maxRetries: 5, retryDelay: 200 });
    }
})().catch(error => { console.error(error.message); process.exitCode = 1; });
