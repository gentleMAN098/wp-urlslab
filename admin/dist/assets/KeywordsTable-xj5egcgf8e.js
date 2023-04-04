import{r as v,R as t,L as $}from"../main-xj5egcgf8e.js";import{u as P,a as q,b as B,L as f,T as h,S as H,M as K,c as O}from"./useTableUpdater-xj5egcgf8e.js";import{I as u,S as C,C as A}from"./datepicker-xj5egcgf8e.js";import"./useMutation-xj5egcgf8e.js";function J({slug:o}){var _;const c="kw_id",{table:E,setTable:b,rowToInsert:s,setInsertRow:i,filters:k,setFilters:T,sortingColumn:p,sortBy:L}=P({slug:o}),g=v.useMemo(()=>`${k}${p}`,[k,p]),{__:r,columnHelper:l,data:d,status:V,isSuccess:I,isFetchingNextPage:F,hasNextPage:R,ref:z}=q({key:o,url:g,pageId:c}),{row:y,rowsSelected:M,selectRow:N,deleteRow:S,updateRow:m}=B({data:d,url:g,slug:o,pageId:c}),w={M:r("Manual"),I:r("Imported"),X:r("None")},a={keyword:r("Keyword"),kwType:r("Type"),kw_length:r("Length"),kw_priority:r("Priority"),kw_usage_count:r("Usage"),lang:r("Language"),link_usage_count:r("Link Usage"),urlFilter:r("URL Filter"),urlLink:r("Link")},x={keyword:t.createElement(u,{liveUpdate:!0,defaultValue:"",label:a.keyword,onChange:e=>i({...s,keyword:e}),required:!0}),kwType:t.createElement(C,{autoClose:!0,items:w,name:"kwType",checkedId:"M",onChange:e=>i({...s,kwType:e})},a.kwType),kw_priority:t.createElement(u,{liveUpdate:!0,type:"number",defaultValue:"0",min:"0",max:"255",label:a.kw_priority,onChange:e=>i({...s,kw_priority:e})}),lang:t.createElement(f,{autoClose:!0,checkedId:"all",onChange:e=>i({...s,lang:e})},r("Language")),urlFilter:t.createElement(u,{liveUpdate:!0,defaultValue:"",label:a.urlFilter,onChange:e=>i({...s,urlFilter:e})}),urlLink:t.createElement(u,{liveUpdate:!0,type:"url",defaultValue:"",label:a.urlLink,onChange:e=>i({...s,urlLink:e}),required:!0})},U=[l.accessor("check",{className:"checkbox",cell:e=>t.createElement(A,{checked:e.row.getIsSelected(),onChange:n=>{N(n,e)}}),header:null,enableResizing:!1}),l.accessor("keyword",{tooltip:e=>t.createElement(h,null,e.getValue()),header:a.keyword,minSize:150}),l.accessor("kwType",{filterValMenu:w,className:"nolimit",cell:e=>t.createElement(C,{items:w,name:e.column.id,checkedId:e.getValue(),onChange:n=>m({newVal:n,cell:e})}),header:a.kwType,size:100}),l.accessor("kw_length",{header:a.kw_length,size:80}),l.accessor("kw_priority",{className:"nolimit",cell:e=>t.createElement(u,{type:"number",defaultValue:e.getValue(),onChange:n=>m({newVal:n,cell:e})}),header:a.kw_priority,size:80}),l.accessor("lang",{className:"nolimit",cell:e=>t.createElement(f,{checkedId:e==null?void 0:e.getValue(),onChange:n=>m({newVal:n,cell:e})}),header:a.lang,size:165}),l.accessor("kw_usage_count",{header:a.kw_usage_count,size:70}),l.accessor("link_usage_count",{header:a.link_usage_count,size:100}),l.accessor("urlFilter",{className:"nolimit",cell:e=>t.createElement(u,{defaultValue:e.renderValue(),onChange:n=>m({newVal:n,cell:e})}),header:a.urlFilter,size:100}),l.accessor("urlLink",{tooltip:e=>t.createElement(h,null,e.getValue()),cell:e=>t.createElement("a",{href:e.getValue(),target:"_blank",rel:"noreferrer"},e.getValue()),header:a.urlLink,enableResizing:!1,size:350}),l.accessor("delete",{className:"deleteRow",cell:e=>t.createElement(H,{onClick:()=>S({cell:e})}),header:null})];return V==="loading"?t.createElement($,null):t.createElement(t.Fragment,null,t.createElement(K,{slug:o,header:a,table:E,rowsSelected:M,onSort:e=>L(e),onFilter:e=>T(e),onClearRow:e=>e&&i(),insertOptions:{inserterCells:x,title:"Add keyword",data:d,slug:o,url:g,pageId:c,rowToInsert:s},exportOptions:{url:o,filters:k,fromId:`from_${c}`,pageId:c,deleteCSVCols:[c,"dest_url_id"]}}),t.createElement(O,{className:"fadeInto",slug:o,returnTable:e=>b(e),columns:U,data:I&&((_=d==null?void 0:d.pages)==null?void 0:_.flatMap(e=>e??[]))},y?t.createElement(h,{center:!0},`${a.keyword} “${y.keyword}”`," ",r("has been deleted.")):null,t.createElement("button",{ref:z},F?"Loading more...":R)))}export{J as default};
