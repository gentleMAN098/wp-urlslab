import{r as I,R as s,L as M}from"../main-jshfvxphaxk.js";import{u as R,a as $,b as y,T as d,S as L,M as P,c as B}from"./useTableUpdater-jshfvxphaxk.js";import{C as D,S as U}from"./datepicker-jshfvxphaxk.js";import"./useMutation-jshfvxphaxk.js";function q({slug:r}){var m;const l="fileid",{table:w,setTable:b,filters:o,setFilters:E,sortingColumn:g,sortBy:V}=R({slug:r}),h=I.useMemo(()=>`${o}${g}`,[o,g]),{__:a,columnHelper:t,data:n,status:_,isSuccess:z,isFetchingNextPage:S,hasNextPage:p,ref:x}=$({key:r,url:h,pageId:l}),{row:u,selectRow:F,deleteRow:T,updateRow:k}=y({data:n,url:h,slug:r,pageId:l}),f={N:a("New"),A:a("Available"),P:a("Processing"),D:a("Disabled")},i={filename:a("File Name"),filetype:a("File Type"),status_changed:a("Status changed"),filestatus:a("Status"),height:a("Height"),width:a("Width"),filesize:a("File Size"),file_usage_count:a("File Usage"),url:a("URL")},C=[t.accessor("check",{className:"checkbox",cell:e=>s.createElement(D,{checked:e.row.getIsSelected(),onChange:c=>{F(c,e)}}),header:null}),t==null?void 0:t.accessor("filename",{tooltip:e=>s.createElement(d,null,e.getValue()),header:i.filename,size:150}),t==null?void 0:t.accessor("filetype",{header:i.filetype,size:80}),t==null?void 0:t.accessor("filestatus",{filterValMenu:f,className:"nolimit",cell:e=>s.createElement(U,{items:f,name:e.column.id,checkedId:e.getValue(),onChange:c=>k({newVal:c,cell:e})}),header:i.filestatus,size:100}),t==null?void 0:t.accessor("status_changed",{cell:e=>new Date(e==null?void 0:e.getValue()).toLocaleString(window.navigator.language),header:()=>a("Status changed"),size:100}),t==null?void 0:t.accessor("width",{cell:e=>`${e.getValue()} px`,header:i.width,size:50}),t==null?void 0:t.accessor("height",{cell:e=>`${e.getValue()} px`,header:i.height,size:50}),t==null?void 0:t.accessor("filesize",{cell:e=>`${Math.round(e.getValue()/1024,0)} kB`,header:i.filesize,size:80}),t==null?void 0:t.accessor("file_usage_count",{header:i.file_usage_count,size:80}),t==null?void 0:t.accessor("url",{tooltip:e=>{const c=/(jpeg|jpg|webp|gif|png|svg)/g,N=e.getValue().search(c);return s.createElement(d,null,N!==-1&&s.createElement("img",{src:e.getValue(),alt:"url"}))},cell:e=>s.createElement("a",{href:e.getValue(),title:e.getValue(),target:"_blank",rel:"noreferrer"},e.getValue()),header:i.url,size:250}),t.accessor("delete",{className:"deleteRow",cell:e=>s.createElement(L,{onClick:()=>T({cell:e})}),header:null})];return _==="loading"?s.createElement(M,null):s.createElement(s.Fragment,null,s.createElement(P,{slug:r,header:i,table:w,onSort:e=>V(e),onFilter:e=>E(e),exportOptions:{url:r,filters:o,fromId:`from_${l}`,pageId:l,deleteCSVCols:[l,"dest_url_id"]}}),s.createElement(B,{className:"fadeInto",slug:r,returnTable:e=>b(e),columns:C,data:z&&((m=n==null?void 0:n.pages)==null?void 0:m.flatMap(e=>e??[]))},u?s.createElement(d,{center:!0},`${i.filename} “${u.filename}”`," ",a("has been deleted.")):null,s.createElement("button",{ref:x},S?"Loading more...":p)))}export{q as default};
