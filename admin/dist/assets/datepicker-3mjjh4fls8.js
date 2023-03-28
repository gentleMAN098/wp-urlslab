import{r,R as e}from"../main-3mjjh4fls8.js";function g({defaultValue:h,placeholder:b,message:c,liveUpdate:u,className:m,type:l,disabled:p,label:i,labelInline:k,onChange:a,children:o,style:s}){const[d,f]=r.useState(h||"");r.useState(!1);const _=/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i,v=t=>{a&&a(l==="number"?t.target.valueAsNumber:t.target.value)},E=()=>d?l==="email"&&_.test(d)?"has-value success":l!=="email"?"has-value":"has-value error":"";return e.createElement("label",{className:`urlslab-inputField-wrap ${m||""} ${k?"inline":""} ${E()}`,style:s},i?e.createElement("span",{className:"urlslab-inputField-label"},i):null,e.createElement("div",{className:`urlslab-inputField ${o?"has-svg":""}`},o,e.createElement("input",{className:"urlslab-input input__text",type:l||"text",defaultValue:d,onChange:t=>{f(t.target.value),u&&v(t)},onBlur:t=>v(t),onKeyDown:t=>{(t.key==="Enter"||t.keyCode===9)&&t.target.blur()},placeholder:b,disabled:p?"disabled":""})),c!=null&&c.length&&E().length?e.createElement("div",{className:"urlslab-inputField-message"},c):null)}function M({checked:h,readOnly:b,radial:c,name:u,className:m,onChange:l,textBefore:p,children:i}){const[k,a]=r.useState(!!h),o=s=>{l&&!b&&l(s.target.checked),b||a(s.target.checked)};return e.createElement("label",{className:`urlslab-checkbox ${m||""} ${p?"textBefore":""} ${c?"radial":""}`},e.createElement("input",{className:`urlslab-checkbox-input ${h?"checked":""}`,type:u?"radio":"checkbox",name:u||"",defaultChecked:k,onChange:s=>o(s)}),e.createElement("div",{className:"urlslab-checkbox-box"}),e.createElement("span",{className:"urlslab-checkbox-text",dangerouslySetInnerHTML:{__html:i}}))}function C({className:h,name:b,style:c,children:u,items:m,checkedId:l,autoClose:p,isFilter:i,onChange:k}){const[a,o]=r.useState(!1),[s,d]=r.useState(!1),[f,_]=r.useState(l),v=r.useRef(!1),E=r.useRef(b);r.useEffect(()=>{const n=x=>{var $;!(($=E.current)!=null&&$.contains(x.target))&&a&&(o(!1),d(!1))};k&&v.current&&!a&&f!==l&&k(f),v.current=!0,document.addEventListener("click",n,!0)},[f,a]);const t=n=>{_(n),p&&(o(!1),d(!1))},N=()=>{o(!a),setTimeout(()=>{d(!s)},100)};return e.createElement("div",{className:`urlslab-FilterMenu urlslab-SortMenu ${h||""} ${a?"active":""}`,style:c,ref:E},!i&&u?e.createElement("div",{className:"urlslab-inputField-label",dangerouslySetInnerHTML:{__html:u}}):null,e.createElement("div",{className:`urlslab-FilterMenu__title ${i?"isFilter":""} ${a?"active":""}`,onClick:N,onKeyUp:n=>N(),role:"button",tabIndex:0},e.createElement("span",{dangerouslySetInnerHTML:{__html:i?u:m[f]}})),e.createElement("div",{className:`urlslab-FilterMenu__items ${a?"active":""} ${s?"visible":""}`},e.createElement("div",{className:`urlslab-FilterMenu__items--inn ${Object.values(m).length>8?"has-scrollbar":""}`},Object.entries(m).map(([n,x])=>e.createElement(M,{className:"urlslab-FilterMenu__item",key:n,id:n,onChange:()=>t(n),name:b,checked:n===f,radial:!0},x)))))}export{M as C,g as I,C as S};
