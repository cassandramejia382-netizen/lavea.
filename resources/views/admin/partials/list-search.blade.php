@once
<style>
.lavea-admin-content .list-search {
    display: flex;
    gap: 8px;
    margin-bottom: 17px;
}
.lavea-admin-content .list-search input {
    flex: 1;
    min-width: 0;
    padding: 11px;
    border: 1px solid #dfe5ef;
    border-radius: 7px;
    background: #fff;
    color: #263550;
    font: inherit;
}
.lavea-admin-content .list-search button {
    padding: 10px 14px;
    border: 0;
    border-radius: 7px;
    background: #4169c8;
    color: #fff;
    font: inherit;
    font-weight: 600;
    cursor: pointer;
}
.lavea-admin-content .list-search button:hover {
    background: #3459b1;
}
</style>
@endonce
<form class="list-search" method="GET" action="{{ route($searchRoute) }}">
    <input type="search" name="search" value="{{ $search }}" placeholder="{{ $searchPlaceholder }}" aria-label="{{ $searchPlaceholder }}">
    <button type="submit">Search</button>
</form>
