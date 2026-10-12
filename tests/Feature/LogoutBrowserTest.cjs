const { spawn, execFileSync } = require('node:child_process');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const assert = require('node:assert/strict');

const workspace = path.resolve(__dirname, '../..');
const temporary = fs.mkdtempSync(path.join(os.tmpdir(), 'lavea-logout-browser-'));
const database = path.join(temporary, 'database.sqlite');
fs.writeFileSync(database, '');
const environment = { ...process.env, APP_ENV: 'testing', DB_CONNECTION: 'sqlite', DB_DATABASE: database, DB_URL: '', SESSION_DRIVER: 'file', SESSION_COOKIE: 'lavea_logout_browser_test', CACHE_STORE: 'array', QUEUE_CONNECTION: 'sync' };
const base = 'http://127.0.0.1:9890';
const chromePath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const phpPath = process.env.PHP_BINARY || path.join(os.homedir(), '.config/herd/bin/php84/php.exe');
let server, chrome, socket;
const pause = ms => new Promise(resolve => setTimeout(resolve, ms));
async function until(check, label) {
    for (let attempt = 0; attempt < 150; attempt++) {
        try { const result = await check(); if (result) return result; } catch {}
        await pause(100);
    }
    throw new Error('Timed out: ' + label);
}

(async () => {
    try {
        execFileSync(phpPath, ['tests/Feature/LogoutBrowserFixture.php'], { cwd: workspace, env: environment, stdio: 'pipe' });
        server = spawn(phpPath, ['artisan', 'serve', '--host=127.0.0.1', '--port=9890', '--no-reload', '--no-interaction'], { cwd: workspace, env: environment, windowsHide: true, stdio: 'ignore' });
        await until(async () => (await fetch(base + '/login')).ok, 'Laravel server');
        chrome = spawn(chromePath, ['--headless=new', '--remote-debugging-port=9892', '--user-data-dir=' + path.join(temporary, 'chrome'), '--no-first-run', '--no-default-browser-check', 'about:blank'], { windowsHide: true, stdio: 'ignore' });
        const target = await until(async () => (await (await fetch('http://127.0.0.1:9892/json')).json()).find(tab => tab.type === 'page'), 'Chrome');
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
            if (result.exceptionDetails) throw new Error(result.exceptionDetails.text);
            return result.result.value;
        };
        await send('Page.enable');
        await send('Network.enable');
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
                console.log(`Landing ${width}px: ${destination} mouse click PASSED`);
            }
        }
        await send('Emulation.clearDeviceMetricsOverride');
        for (const role of ['admin', 'staff', 'customer']) {
            await send('Network.clearBrowserCookies');
            await send('Page.navigate', { url: base + '/login' });
            await until(() => evaluate('!!document.querySelector("input[name=password]")'), 'login form');
            await evaluate(`document.querySelector('[name=email]').value = ${JSON.stringify(role + '@logout-browser.test')}; document.querySelector('[name=password]').value = 'Browser-test-password-123'; document.querySelector('[name=password]').form.requestSubmit();`);
            await until(() => evaluate(`location.pathname === '/${role}/dashboard' && document.readyState === 'complete'`), role + ' dashboard');
            const history = await send('Page.getNavigationHistory');
            const dashboardEntry = history.entries[history.currentIndex];
            const headers = await evaluate(`fetch(location.href, {cache:'no-store'}).then(r => ({cache:r.headers.get('cache-control'), pragma:r.headers.get('pragma'), expires:r.headers.get('expires')}))`);
            assert(headers.cache.includes('no-store') && headers.cache.includes('no-cache') && headers.cache.includes('must-revalidate') && headers.cache.includes('max-age=0'));
            assert.equal(headers.pragma, 'no-cache'); assert.equal(headers.expires, '0');
            await evaluate(`document.querySelector('a[href$="/logout/confirm"]').click()`);
            await until(() => evaluate(`location.pathname === '/logout/confirm' && !!document.querySelector('form[action$="/logout"]')`), 'logout confirmation');
            await evaluate(`document.querySelector('form[action$="/logout"]').requestSubmit()`);
            await until(() => evaluate(`location.pathname === '/login' && !!document.querySelector('[name=password]')`), 'logout login redirect');
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
        socket?.close();
        if (chrome) execFileSync('taskkill', ['/PID', String(chrome.pid), '/T', '/F'], { stdio: 'ignore' });
        if (server) execFileSync('taskkill', ['/PID', String(server.pid), '/T', '/F'], { stdio: 'ignore' });
        const resolved = path.resolve(temporary);
        if (resolved.startsWith(path.resolve(os.tmpdir()) + path.sep) && path.basename(resolved).startsWith('lavea-logout-browser-')) fs.rmSync(resolved, { recursive: true, force: true, maxRetries: 5, retryDelay: 200 });
    }
})().catch(error => { console.error(error.message); process.exitCode = 1; });
