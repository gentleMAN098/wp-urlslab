import{r as k,R as t,L as y}from"../main.js";import{u as z,a as F,b as L,T as o,S as N,M,c as q}from"./useTableUpdater-b0461853.js";import{C as $}from"./datepicker-57b9c744.js";import"./useMutation-cf66945e.js";function G({slug:l}){var m;const n="generator_id",{table:g,setTable:h,filters:u,setFilters:p,currentFilters:E,sortingColumn:i,sortBy:f}=z({slug:l}),d=k.useMemo(()=>`${u}${i}`,[u,i]),{__:s,columnHelper:a,data:c,status:b,isSuccess:w,isFetchingNextPage:x,hasNextPage:C,ref:R}=F({key:l,url:d,pageId:n,currentFilters:E,sortingColumn:i}),{row:S,selectRow:T,deleteRow:V}=L({data:c,url:d,slug:l,pageId:n}),r={query:s("Query"),context:s("Context"),lang:s("Language code"),status:s("Status"),status_changed:s("Changed at"),results:s("Results")},_=[a.accessor("check",{className:"checkbox",cell:e=>t.createElement($,{checked:e.row.getIsSelected(),onChange:I=>{T(I,e)}}),header:null}),a.accessor("query",{tooltip:e=>t.createElement(o,null,e.getValue()),header:r.query,size:500}),a.accessor("context",{tooltip:e=>t.createElement(o,null,e.getValue()),header:r.context,size:500}),a.accessor("lang",{tooltip:e=>t.createElement(o,null,e.getValue()),header:r.lang,size:500}),a.accessor("results",{tooltip:e=>t.createElement(o,null,e.getValue()),header:r.results,size:500}),a.accessor("status",{tooltip:e=>t.createElement(o,null,e.getValue()),header:r.status,size:500}),a.accessor("status_changed",{cell:e=>new Date(e==null?void 0:e.getValue()).toLocaleString(window.navigator.language),header:r.status_changed,size:100}),a.accessor("delete",{className:"deleteRow",cell:e=>t.createElement(N,{onClick:()=>V({cell:e})}),header:null})];return b==="loading"?t.createElement(y,null):t.createElement(t.Fragment,null,t.createElement(M,{slug:l,header:r,table:g,noImport:!0,onSort:e=>f(e),onFilter:e=>p(e),exportOptions:{url:l,filters:u,fromId:`from_${n}`,pageId:n,deleteCSVCols:[n,"generator_id"]}}),t.createElement(q,{className:"fadeInto",columns:_,returnTable:e=>h(e),data:w&&((m=c==null?void 0:c.pages)==null?void 0:m.flatMap(e=>e??[]))},S?t.createElement(o,{center:!0},s("Row has been deleted.")):null,t.createElement("button",{ref:R},x?"Loading more...":C)))}export{G as default};
