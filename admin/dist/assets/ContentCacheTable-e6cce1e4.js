import{r as x,R as t,L as F}from"../main.js";import{u as M,a as w,T as z,M as I,c as L}from"./useTableUpdater-b0461853.js";import"./datepicker-57b9c744.js";import"./useMutation-cf66945e.js";function $({slug:a}){var i;const u="cache_crc32",{table:d,setTable:h,filters:s,setFilters:m,sortingColumn:l,sortBy:g}=M({slug:a}),p=x.useMemo(()=>`${s}${l}`,[s,l]),{__:c,columnHelper:o,data:r,status:_,isSuccess:f,isFetchingNextPage:E,hasNextPage:b,ref:T}=w({key:a,url:p,pageId:u}),n={date_changed:c("Changed at"),cache_len:c("Cache size"),cache_content:c("Cache content")},C=[o.accessor("date_changed",{cell:e=>new Date(e==null?void 0:e.getValue()).toLocaleString(window.navigator.language),header:n.date_changed,size:100}),o.accessor("cache_len",{cell:e=>`${Math.round(e.getValue()/1024,0)} kB`,header:n.cache_len,size:100}),o.accessor("cache_content",{tooltip:e=>t.createElement(z,null,e.getValue()),header:n.cache_content,size:500})];return _==="loading"?t.createElement(F,null):t.createElement(t.Fragment,null,t.createElement(I,{slug:a,header:n,table:d,noDelete:!0,noExport:!0,noImport:!0,onSort:e=>g(e),onFilter:e=>m(e)}),t.createElement(L,{className:"fadeInto",columns:C,slug:a,returnTable:e=>h(e),data:f&&((i=r==null?void 0:r.pages)==null?void 0:i.flatMap(e=>e??[]))},t.createElement("button",{ref:T},E?"Loading more...":b)))}export{$ as default};
