import{R as e,u as i,r as t,_ as a}from"../main-xj5egcgf8e.js";import{M as h,O as d}from"./ModuleViewHeader-xj5egcgf8e.js";import"./api-exclamation-xj5egcgf8e.js";/* empty css                            */const E=""+new URL("liveagent_screenshot-xj5egcgf8e.jpeg",import.meta.url).href;function p(){return e.createElement(e.Fragment,null,e.createElement("p",null,"Screenshots are a great way to grab an audience's attention and make your content more appealing. With this module, you can easily add automatically generated screenshots via a shortcode into the content. It will not only save you time but will also give your content a professional look."),e.createElement("p",null,"Using the Automated Screenshots module can be especially useful for websites with many pages, where manually taking screenshots for each one can be time-consuming. With the module, you can quickly generate screenshots for each page."),e.createElement("p",null,"Overall, the module makes screenshots easy to use with zero effort. It is a great way to save time and make your content stand out."),e.createElement("h4",null,"How to use the feature?"),e.createElement("p",null,"It's almost effortless and will only take a maximum of five minutes. All you have to do is add a shortcode to your theme template, and the module will take care of the rest for you."),e.createElement("h4",null,"Shortcode"),e.createElement("code",null,'[urlslab-screenshot screenshot-type="carousel" url="https://www.liveagent.com" alt="Home" width="100%" height="100%" default-image="https://www.yourdomain.com/default_image.jpg"]'),e.createElement("p",null,e.createElement("strong",null,"Shortcode Attributes")),e.createElement("table",{border:"1"},e.createElement("tbody",null,e.createElement("tr",null,e.createElement("th",null,"Attribute"),e.createElement("th",null,"Required"),e.createElement("th",null,"Description"),e.createElement("th",null,"Default Value"),e.createElement("th",null,"Possible Values")),e.createElement("tr",null,e.createElement("td",null,"screenshot-type"),e.createElement("td",null,"optional"),e.createElement("td",null," "),e.createElement("td",null,"carousel"),e.createElement("td",null,"carousel, full-page, carousel-thumbnail, full-page-thumbnail")),e.createElement("tr",null,e.createElement("td",null,"url"),e.createElement("td",null,"mandatory"),e.createElement("td",null,"Link to the page from which a screenshot should be taken."),e.createElement("td",null," "),e.createElement("td",null," ")),e.createElement("tr",null,e.createElement("td",null,"alt"),e.createElement("td",null,"optional"),e.createElement("td",null,"Value of the image alt text."),e.createElement("td",null,"A summary of the destination URL"),e.createElement("td",null," ")),e.createElement("tr",null,e.createElement("td",null,"width"),e.createElement("td",null,"optional"),e.createElement("td",null,"The width of the image."),e.createElement("td",null,"100%"),e.createElement("td",null," ")),e.createElement("tr",null,e.createElement("td",null,"height"),e.createElement("td",null,"optional"),e.createElement("td",null,"The height of the image."),e.createElement("td",null,"100%"),e.createElement("td",null," ")),e.createElement("tr",null,e.createElement("td",null,"default-image"),e.createElement("td",null,"optional"),e.createElement("td",null,"The URL of the default image in case we don't yet have the screenshot."),e.createElement("td",null,"-"),e.createElement("td",null," ")))),e.createElement("h4",null,"Example"),e.createElement("p",null,"Example of shortcode to include a screenshot of www.liveagent.com to your website content: ",e.createElement("code",null,'[urlslab-screenshot url="https://www.liveagent.com"]')),e.createElement("img",{src:E,alt:"Example of the screenshot for the URL www.liveagent.com"}))}function v({moduleId:n}){const{__:r}=i(),[l,o]=t.useState("overview"),c=new Map([["screenshot",r("Screenshots")]]),u=t.lazy(()=>a(()=>import("./Settings-xj5egcgf8e.js"),["./Settings-xj5egcgf8e.js","../main-xj5egcgf8e.js","./main.css","./datepicker-xj5egcgf8e.js","./datepicker-xj5egcgf8e.css","./Switch-xj5egcgf8e.js","./Switch-xj5egcgf8e.css","./useMutation-xj5egcgf8e.js","./Settings-xj5egcgf8e.css"],import.meta.url)),m=t.lazy(()=>a(()=>import("./ScreenshotTable-xj5egcgf8e.js"),["./ScreenshotTable-xj5egcgf8e.js","../main-xj5egcgf8e.js","./main.css","./useTableUpdater-xj5egcgf8e.js","./datepicker-xj5egcgf8e.js","./datepicker-xj5egcgf8e.css","./useMutation-xj5egcgf8e.js","./useTableUpdater-xj5egcgf8e.css"],import.meta.url));return e.createElement("div",{className:"urlslab-tableView"},e.createElement(h,{moduleMenu:c,activeMenu:s=>o(s)}),l==="overview"&&e.createElement(d,{moduleId:n},e.createElement(p,null)),l==="screenshot"&&e.createElement(t.Suspense,null,e.createElement(m,{slug:"screenshot"})),l==="settings"&&e.createElement(t.Suspense,null,e.createElement(u,{className:"fadeInto",settingId:n})))}export{v as default};
