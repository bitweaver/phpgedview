/*
Copyright Scand LLC http://www.scbr.com
This version of Software is free for using under GPL. For other purposes please contact info@scbr.com to obtain Commercial or Enterprise license (Professional Edition will be included)
*/function dhx_init_tabbars(){
 var z=document.getElementsByTagName("div");
 for(var i=0;i<z.length;i++)
 if(z[i].className.indexOf("dhtmlxTabBar")!=-1){
 var n=z[i];var id=n.id;

 var k=new Array();
 for(var j=0;j<n.childNodes.length;j++)
 if(n.childNodes[j].tagName)
 k[k.length]=n.childNodes[j];

 var w=new dhtmlXTabBar(id,n.getAttribute("mode")||"top",n.getAttribute("tabheight")||20);

 w.setImagePath(n.getAttribute("imgpath")||"js/scbr/dhtmlXTabbar/imgs/"); // PGV

 var acs=n.getAttribute("margin");
 if(acs!=null)w._margin=acs;

 acs=n.getAttribute("align");
 if(acs)w._align=acs;

 acs=n.getAttribute("hrefmode");
 if(acs)w.setHrefMode(acs);

 acs=n.getAttribute("offset");
 if(acs!=null)w._offset=acs;

 acs=n.getAttribute("tabstyle");
 if(acs!=null)w.setStyle(acs);

 acs=n.getAttribute("select");

 var clrs=n.getAttribute("skinColors");
 if(clrs)w.setSkinColors(clrs.split(",")[0],clrs.split(",")[1]);

 for(var j=0;j<k.length;j++)
{
 var m=k[j];
 w.addTab(m.id,m.getAttribute("name"),m.getAttribute("width")||(m.getAttribute("name").length*6)+14,null,m.getAttribute("row")); // PGV
 var href=m.getAttribute("href");
 if(href)w.setContentHref(m.id,href);
 else w.setContent(m.id,m);

 if(m.style.display=="none")
 m.style.display="";
}
w.enableAutoSize(true, true);
 if(k.length)w.setTabActive(acs||k[0].id);
 window[id]=w;
}
}

if(window.addEventListener)window.addEventListener("load",dhx_init_tabbars,false);
else if(window.attachEvent)window.attachEvent("onload",dhx_init_tabbars);





