
try{if(typeof(_jmol)!="undefined")exit()
var defaultdir="."
var defaultjar="JmolApplet.jar"
var undefined;function jmolInitialize(codebaseDirectory,fileNameOrUseSignedApplet){if(_jmol.initialized)
return;_jmol.initialized=true;if(_jmol.jmoljar){var f=_jmol.jmoljar;if(f.indexOf("/")>=0){alert("This web page URL is requesting that the applet used be "+f+". This is a possible security risk, particularly if the applet is signed, because signed applets can read and write files on your local machine or network.")
var ok=prompt("Do you want to use applet "+f+"? ","yes or no")
if(ok=="yes"){codebaseDirectory=f.substring(0,f.lastIndexOf("/"));fileNameOrUseSignedApplet=f.substring(f.lastIndexOf("/")+1);}else{_jmolGetJarFilename(fileNameOrUseSignedApplet);alert("The web page URL was ignored. Continuing using "+_jmol.archivePath+' in directory "'+codebaseDirectory+'"');}}else{fileNameOrUseSignedApplet=f;}}
_jmolSetCodebase(codebaseDirectory);_jmolGetJarFilename(fileNameOrUseSignedApplet);_jmolOnloadResetForms();}
function jmolSetTranslation(TF){_jmol.params.doTranslate=''+TF;}
function _jmolGetJarFilename(fileNameOrFlag){_jmol.archivePath=(typeof(fileNameOrFlag)=="string"?fileNameOrFlag:(fileNameOrFlag?"JmolAppletSigned":"JmolApplet")+"0.jar");}
function jmolSetDocument(doc){_jmol.currentDocument=doc;}
function jmolSetAppletColor(boxbgcolor,boxfgcolor,progresscolor){_jmolInitCheck();_jmol.params.boxbgcolor=boxbgcolor;if(boxfgcolor)
_jmol.params.boxfgcolor=boxfgcolor
else if(boxbgcolor=="white"||boxbgcolor=="#FFFFFF")
_jmol.params.boxfgcolor="black";else
_jmol.params.boxfgcolor="white";if(progresscolor)
_jmol.params.progresscolor=progresscolor;if(_jmol.debugAlert)
alert(" boxbgcolor="+_jmol.params.boxbgcolor+" boxfgcolor="+_jmol.params.boxfgcolor+" progresscolor="+_jmol.params.progresscolor);}
function jmolSetAppletWindow(w){_jmol.appletWindow=w;}
function jmolApplet(size,script,nameSuffix){_jmolInitCheck();return _jmolApplet(size,null,script,nameSuffix);}
function jmolButton(script,label,id,title){_jmolInitCheck();id!=undefined&&id!=null||(id="jmolButton"+_jmol.buttonCount);label!=undefined&&label!=null||(label=script.substring(0,32));++_jmol.buttonCount;var scriptIndex=_jmolAddScript(script);var t="<span id=\"span_"+id+"\""+(title?" title=\""+title+"\"":"")+"><input type='button' name='"+id+"' id='"+id+"' value='"+label+"' onclick='_jmolClick(this,"+scriptIndex+_jmol.targetText+")' onmouseover='_jmolMouseOver("+scriptIndex+");return true' onmouseout='_jmolMouseOut()' "+
_jmol.buttonCssText+" /></span>";if(_jmol.debugAlert)
alert(t);return _jmolDocumentWrite(t);}
function jmolCheckbox(scriptWhenChecked,scriptWhenUnchecked,labelHtml,isChecked,id,title){_jmolInitCheck();id!=undefined&&id!=null||(id="jmolCheckbox"+_jmol.checkboxCount);++_jmol.checkboxCount;if(scriptWhenChecked==undefined||scriptWhenChecked==null||scriptWhenUnchecked==undefined||scriptWhenUnchecked==null){alert("jmolCheckbox requires two scripts");return;}
if(labelHtml==undefined||labelHtml==null){alert("jmolCheckbox requires a label");return;}
var indexChecked=_jmolAddScript(scriptWhenChecked);var indexUnchecked=_jmolAddScript(scriptWhenUnchecked);var eospan="</span>"
var t="<span id=\"span_"+id+"\""+(title?" title=\""+title+"\"":"")+"><input type='checkbox' name='"+id+"' id='"+id+"' onclick='_jmolCbClick(this,"+
indexChecked+","+indexUnchecked+_jmol.targetText+")' onmouseover='_jmolCbOver(this,"+indexChecked+","+
indexUnchecked+");return true' onmouseout='_jmolMouseOut()' "+
(isChecked?"checked='true' ":"")+_jmol.checkboxCssText+" />"
if(labelHtml.toLowerCase().indexOf("<td>")>=0){t+=eospan
eospan="";}
t+="<label for=\""+id+"\">"+labelHtml+"</label>"+eospan;if(_jmol.debugAlert)
alert(t);return _jmolDocumentWrite(t);}
function jmolStartNewRadioGroup(){++_jmol.radioGroupCount;}
function jmolRadioGroup(arrayOfRadioButtons,separatorHtml,groupName,id,title){_jmolInitCheck();var type=typeof arrayOfRadioButtons;if(type!="object"||type==null||!arrayOfRadioButtons.length){alert("invalid arrayOfRadioButtons");return;}
separatorHtml!=undefined&&separatorHtml!=null||(separatorHtml="&nbsp; ");var len=arrayOfRadioButtons.length;jmolStartNewRadioGroup();groupName||(groupName="jmolRadioGroup"+(_jmol.radioGroupCount-1));var t="<span id='"+(id?id:groupName)+"'>";for(var i=0;i<len;++i){if(i==len-1)
separatorHtml="";var radio=arrayOfRadioButtons[i];type=typeof radio;if(type=="object"){t+=_jmolRadio(radio[0],radio[1],radio[2],separatorHtml,groupName,(radio.length>3?radio[3]:(id?id:groupName)+"_"+i),(radio.length>4?radio[4]:0),title);}else{t+=_jmolRadio(radio,null,null,separatorHtml,groupName,(id?id:groupName)+"_"+i,title);}}
t+="</span>"
if(_jmol.debugAlert)
alert(t);return _jmolDocumentWrite(t);}
function jmolRadio(script,labelHtml,isChecked,separatorHtml,groupName,id,title){_jmolInitCheck();if(_jmol.radioGroupCount==0)
++_jmol.radioGroupCount;var t=_jmolRadio(script,labelHtml,isChecked,separatorHtml,groupName,(id?id:groupName+"_"+_jmol.radioCount),title?title:0);if(_jmol.debugAlert)
alert(t);return _jmolDocumentWrite(t);}
function jmolLink(script,label,id,title){_jmolInitCheck();id!=undefined&&id!=null||(id="jmolLink"+_jmol.linkCount);label!=undefined&&label!=null||(label=script.substring(0,32));++_jmol.linkCount;var scriptIndex=_jmolAddScript(script);var t="<span id=\"span_"+id+"\""+(title?" title=\""+title+"\"":"")+"><a name='"+id+"' id='"+id+"' href='javascript:_jmolClick(this,"+scriptIndex+_jmol.targetText+");' onmouseover='_jmolMouseOver("+scriptIndex+");return true;' onmouseout='_jmolMouseOut()' "+
_jmol.linkCssText+">"+label+"</a></span>";if(_jmol.debugAlert)
alert(t);return _jmolDocumentWrite(t);}
function jmolCommandInput(label,size,id,title){_jmolInitCheck();id!=undefined&&id!=null||(id="jmolCmd"+_jmol.cmdCount);label!=undefined&&label!=null||(label="Execute");size!=undefined&&!isNaN(size)||(size=60);++_jmol.cmdCount;var t="<span id=\"span_"+id+"\""+(title?" title=\""+title+"\"":"")+"><input name='"+id+"' id='"+id+"' size='"+size+"' onkeypress='_jmolCommandKeyPress(event,\""+id+"\""+_jmol.targetText+")'><input type=button value = '"+label+"' onclick='jmolScript(document.getElementById(\""+id+"\").value"+_jmol.targetText+")' /></span>";if(_jmol.debugAlert)
alert(t);return _jmolDocumentWrite(t);}
function _jmolCommandKeyPress(e,id,target){var keycode=(window.event?window.event.keyCode:e?e.which:0);if(keycode==13){var inputBox=document.getElementById(id)
_jmolScriptExecute(inputBox,inputBox.value,target)}}
function _jmolScriptExecute(element,script,target){if(typeof(script)=="object")
script[0](element,script,target)
else
jmolScript(script,target)}
function jmolMenu(arrayOfMenuItems,size,id,title){_jmolInitCheck();id!=undefined&&id!=null||(id="jmolMenu"+_jmol.menuCount);++_jmol.menuCount;var type=typeof arrayOfMenuItems;if(type!=null&&type=="object"&&arrayOfMenuItems.length){var len=arrayOfMenuItems.length;if(typeof size!="number"||size==1)
size=null;else if(size<0)
size=len;var sizeText=size?" size='"+size+"' ":"";var t="<span id=\"span_"+id+"\""+(title?" title=\""+title+"\"":"")+"><select name='"+id+"' id='"+id+"' onChange='_jmolMenuSelected(this"+_jmol.targetText+")'"+
sizeText+_jmol.menuCssText+">";for(var i=0;i<len;++i){var menuItem=arrayOfMenuItems[i];type=typeof menuItem;var script,text;var isSelected=undefined;if(type=="object"&&menuItem!=null){script=menuItem[0];text=menuItem[1];isSelected=menuItem[2];}else{script=text=menuItem;}
text!=undefined&&text!=null||(text=script);if(script=="#optgroup"){t+="<optgroup label='"+text+"'>";}else if(script=="#optgroupEnd"){t+="</optgroup>";}else{var scriptIndex=_jmolAddScript(script);var selectedText=isSelected?"' selected='true'>":"'>";t+="<option value='"+scriptIndex+selectedText+text+"</option>";}}
t+="</select></span>";if(_jmol.debugAlert)
alert(t);return _jmolDocumentWrite(t);}}
function jmolHtml(html){return _jmolDocumentWrite(html);}
function jmolBr(){return _jmolDocumentWrite("<br />");}
function jmolDebugAlert(enableAlerts){_jmol.debugAlert=(enableAlerts==undefined||enableAlerts)}
function jmolAppletInline(size,inlineModel,script,nameSuffix){_jmolInitCheck();return _jmolApplet(size,_jmolSterilizeInline(inlineModel),script,nameSuffix);}
function jmolSetTarget(targetSuffix){_jmol.targetSuffix=targetSuffix;_jmol.targetText=targetSuffix?",\""+targetSuffix+"\"":",0";}
function jmolScript(script,targetSuffix){if(script){_jmolCheckBrowser();if(targetSuffix=="all"){with(_jmol){for(var i=0;i<appletSuffixes.length;++i){var applet=_jmolGetApplet(appletSuffixes[i]);if(applet)applet.script(script);}}}else{var applet=_jmolGetApplet(targetSuffix);if(applet)applet.script(script);}}}
function jmolLoadInline(model,targetSuffix){if(!model)return"ERROR: NO MODEL"
var applet=_jmolGetApplet(targetSuffix);if(!applet)return"ERROR: NO APPLET"
if(typeof(model)=="string")
return applet.loadInlineString(model,"",false);else
return applet.loadInlineArray(model,"",false);}
function jmolLoadInlineScript(model,script,targetSuffix){if(!model)return"ERROR: NO MODEL"
var applet=_jmolGetApplet(targetSuffix);if(!applet)return"ERROR: NO APPLET"
return applet.loadInlineString(model,script,false);}
function jmolLoadInlineArray(ModelArray,script,targetSuffix){if(!model)return"ERROR: NO MODEL"
script||(script="")
var applet=_jmolGetApplet(targetSuffix);if(!applet)return"ERROR: NO APPLET"
try{return applet.loadInlineArray(ModelArray,script,false);}catch(err){return applet.loadInlineString(ModelArray.join("\n"),script,false);}}
function jmolAppendInlineArray(ModelArray,script,targetSuffix){if(!model)return"ERROR: NO MODEL"
script||(script="")
var applet=_jmolGetApplet(targetSuffix);if(!applet)return"ERROR: NO APPLET"
try{return applet.loadInlineArray(ModelArray,script,true);}catch(err){return applet.loadInlineString(ModelArray.join("\n"),script,true);}}
function jmolAppendInlineScript(model,script,targetSuffix){if(!model)return"ERROR: NO MODEL"
var applet=_jmolGetApplet(targetSuffix);if(!applet)return"ERROR: NO APPLET"
return applet.loadInlineString(model,script,true);}
function jmolCheckBrowser(action,urlOrMessage,nowOrLater){if(typeof action=="string"){action=action.toLowerCase();action=="alert"||action=="redirect"||action=="popup"||(action=null);}
if(typeof action!="string")
alert("jmolCheckBrowser(action, urlOrMessage, nowOrLater)\n\n"+"action must be 'alert', 'redirect', or 'popup'");else{if(typeof urlOrMessage!="string")
alert("jmolCheckBrowser(action, urlOrMessage, nowOrLater)\n\n"+"urlOrMessage must be a string");else{_jmol.checkBrowserAction=action;_jmol.checkBrowserUrlOrMessage=urlOrMessage;}}
if(typeof nowOrLater=="string"&&nowOrLater.toLowerCase()=="now")
_jmolCheckBrowser();}
function jmolSetAppletCssClass(appletCssClass){if(_jmol.hasGetElementById){_jmol.appletCssClass=appletCssClass;_jmol.appletCssText=appletCssClass?"class='"+appletCssClass+"' ":"";}}
function jmolSetButtonCssClass(buttonCssClass){if(_jmol.hasGetElementById){_jmol.buttonCssClass=buttonCssClass;_jmol.buttonCssText=buttonCssClass?"class='"+buttonCssClass+"' ":"";}}
function jmolSetCheckboxCssClass(checkboxCssClass){if(_jmol.hasGetElementById){_jmol.checkboxCssClass=checkboxCssClass;_jmol.checkboxCssText=checkboxCssClass?"class='"+checkboxCssClass+"' ":"";}}
function jmolSetRadioCssClass(radioCssClass){if(_jmol.hasGetElementById){_jmol.radioCssClass=radioCssClass;_jmol.radioCssText=radioCssClass?"class='"+radioCssClass+"' ":"";}}
function jmolSetLinkCssClass(linkCssClass){if(_jmol.hasGetElementById){_jmol.linkCssClass=linkCssClass;_jmol.linkCssText=linkCssClass?"class='"+linkCssClass+"' ":"";}}
function jmolSetMenuCssClass(menuCssClass){if(_jmol.hasGetElementById){_jmol.menuCssClass=menuCssClass;_jmol.menuCssText=menuCssClass?"class='"+menuCssClass+"' ":"";}}
var _jmol={currentDocument:document,debugAlert:false,codebase:"",modelbase:".",appletCount:0,appletSuffixes:[],appletWindow:null,allowedJmolSize:[25,2048,300],buttonCount:0,checkboxCount:0,linkCount:0,cmdCount:0,menuCount:0,radioCount:0,radioGroupCount:0,appletCssClass:null,appletCssText:"",buttonCssClass:null,buttonCssText:"",checkboxCssClass:null,checkboxCssText:"",java_arguments:"-Xmx512m",radioCssClass:null,radioCssText:"",linkCssClass:null,linkCssText:"",menuCssClass:null,menuCssText:"",targetSuffix:0,targetText:",0",scripts:[""],params:{syncId:(""+Math.random()).substring(3),progressbar:"true",progresscolor:"blue",boxbgcolor:"black",boxfgcolor:"white",boxmessage:"Downloading JmolApplet ..."},ua:navigator.userAgent.toLowerCase(),os:"unknown",browser:"unknown",browserVersion:0,hasGetElementById:!!document.getElementById,isJavaEnabled:navigator.javaEnabled(),useIEObject:false,useHtml4Object:false,windowsClassId:"clsid:8AD9C840-044E-11D1-B3E9-00805F499D93",windowsCabUrl:"http://java.sun.com/update/1.6.0/jinstall-6u22-windows-i586.cab",isBrowserCompliant:false,isJavaCompliant:false,isFullyCompliant:false,initialized:false,initChecked:false,browserChecked:false,checkBrowserAction:"alert",checkBrowserUrlOrMessage:null,archivePath:null,previousOnloadHandler:null,jmoljar:null,useNoApplet:false,ready:{}}
with(_jmol){function _jmolTestUA(candidate){var ua=_jmol.ua;var index=ua.indexOf(candidate);if(index<0)
return false;_jmol.browser=candidate;_jmol.browserVersion=parseFloat(ua.substring(index+candidate.length+1));return true;}
function _jmolTestOS(candidate){if(_jmol.ua.indexOf(candidate)<0)
return false;_jmol.os=candidate;return true;}
_jmolTestUA("konqueror")||_jmolTestUA("webkit")||_jmolTestUA("omniweb")||_jmolTestUA("opera")||_jmolTestUA("webtv")||_jmolTestUA("icab")||_jmolTestUA("msie")||(_jmol.ua.indexOf("compatible")<0&&_jmolTestUA("mozilla"));_jmolTestOS("linux")||_jmolTestOS("unix")||_jmolTestOS("mac")||_jmolTestOS("win");isBrowserCompliant=hasGetElementById;if(browser=="opera"&&browserVersion<=7.54&&os=="mac"||browser=="webkit"&&browserVersion<125.12||browser=="msie"&&os=="mac"||browser=="konqueror"&&browserVersion<=3.3){isBrowserCompliant=false;}
isJavaCompliant=isJavaEnabled;isFullyCompliant=isBrowserCompliant&&isJavaCompliant;useIEObject=(os=="win"&&browser=="msie"&&browserVersion>=5.5);useHtml4Object=(browser=="mozilla"&&browserVersion>=5)||(browser=="opera"&&browserVersion>=8)||(browser=="webkit"&&browserVersion>=412.2);try{if(top.location.search.indexOf("JMOLJAR=")>=0)
jmoljar=top.location.search.split("JMOLJAR=")[1].split("&")[0];}catch(e){}
try{useNoApplet=(top.location.search.indexOf("NOAPPLET")>=0);}catch(e){}}
function jmolSetMemoryMb(nMb){_jmol.java_arguments="-Xmx"+Math.round(nMb)+"m"}
function jmolSetParameter(name,value){_jmol.params[name]=value}
function jmolSetCallback(callbackName,funcName){_jmol.params[callbackName]=funcName}
try{if(top.location.search.indexOf("PARAMS=")>=0){var pars=unescape(top.location.search.split("PARAMS=")[1].split("&")[0]).split(";");for(var i=0;i<pars.length;i++){var p=pars[i].split(":");jmolSetParameter(p[0],p[1]);}}}catch(e){}
function jmolSetSyncId(n){return _jmol.params["syncId"]=n}
function jmolGetSyncId(){return _jmol.params["syncId"]}
function jmolSetLogLevel(n){_jmol.params.logLevel=''+n;}
if(noJavaMsg==undefined)var noJavaMsg="You do not have Java applets enabled in your web browser, or your browser is blocking this applet.<br />\n"+"Check the warning message from your browser and/or enable Java applets in<br />\n"+"your web browser preferences, or install the Java Runtime Environment from <a href='http://www.java.com'>www.java.com</a><br />";if(noJavaMsg2==undefined)var noJavaMsg2="You do not have the<br />\n"+"Java Runtime Environment<br />\n"+"installed for applet support.<br />\n"+"Visit <a href='http://www.java.com'>www.java.com</a>";function _jmolApplet(size,inlineModel,script,nameSuffix){with(_jmol){nameSuffix==undefined&&(nameSuffix=appletCount);appletSuffixes.push(nameSuffix);++appletCount;script||(script="select *");var sz=_jmolGetAppletSize(size);var widthAndHeight=" width='"+sz[0]+"' height='"+sz[1]+"' ";var tHeader,tFooter;codebase||jmolInitialize(".");if(useIEObject||useHtml4Object){params.archive=archivePath;params.mayscript='true';params.codebase=codebase;params.code='JmolApplet';tHeader="<object name='jmolApplet"+nameSuffix+"' id='jmolApplet"+nameSuffix+"' "+appletCssText+"\n"+
widthAndHeight+"\n";tFooter="</object>";}
if(java_arguments)
params.java_arguments=java_arguments;if(useIEObject){tHeader+=" classid='"+windowsClassId+"'\n"+
(windowsCabUrl?" codebase='"+windowsCabUrl+"'\n":"")+">\n";}else if(useHtml4Object){tHeader+=" type='application/x-java-applet'\n>\n";}else{tHeader="<applet name='jmolApplet"+nameSuffix+"' id='jmolApplet"+nameSuffix+"' "+appletCssText+"\n"+
widthAndHeight+"\n"+" code='JmolApplet'"+" archive='"+archivePath+"' codebase='"+codebase+"'\n"+" mayscript='true'>\n";tFooter="</applet>";}
var visitJava;if(useIEObject||useHtml4Object){var szX="width:"+sz[0]
if(szX.indexOf("%")==-1)szX+="px"
var szY="height:"+sz[1]
if(szY.indexOf("%")==-1)szY+="px"
visitJava="<p style='background-color:yellow; color:black; "+
szX+";"+szY+";"+"text-align:center;vertical-align:middle;'>\n"+
noJavaMsg+"</p>";}else{visitJava="<table bgcolor='yellow'><tr>"+"<td align='center' valign='middle' "+widthAndHeight+"><font color='black'>\n"+
noJavaMsg2+"</font></td></tr></table>";}
params.loadInline=(inlineModel?inlineModel:"");params.script=(script?_jmolSterilizeScript(script):"");var t=tHeader+_jmolParams()+visitJava+tFooter;jmolSetTarget(nameSuffix);ready["jmolApplet"+nameSuffix]=false;if(_jmol.debugAlert)
alert(t);return _jmolDocumentWrite(t);}}
function _jmolParams(){var t="";for(var i in _jmol.params)
if(_jmol.params[i]!="")
t+="  <param name='"+i+"' value='"+_jmol.params[i]+"' />\n";return t}
function _jmolInitCheck(){if(_jmol.initChecked)
return;_jmol.initChecked=true;jmolInitialize(defaultdir,defaultjar)}
function _jmolCheckBrowser(){with(_jmol){if(browserChecked)
return;browserChecked=true;if(isFullyCompliant)
return true;if(checkBrowserAction=="redirect")
location.href=checkBrowserUrlOrMessage;else if(checkBrowserAction=="popup")
_jmolPopup(checkBrowserUrlOrMessage);else{var msg=checkBrowserUrlOrMessage;if(msg==null)
msg="Your web browser is not fully compatible with Jmol\n\n"+"browser: "+browser+"   version: "+browserVersion+"   os: "+os+"   isBrowserCompliant: "+isBrowserCompliant+"   isJavaCompliant: "+isJavaCompliant+"\n\n"+ua;alert(msg);}}
return false;}
function jmolSetXHTML(id){_jmol.isXHTML=true
_jmol.XhtmlElement=null
_jmol.XhtmlAppendChild=false
if(id){_jmol.XhtmlElement=document.getElementById(id)
_jmol.XhtmlAppendChild=true}}
function _jmolDocumentWrite(text){if(_jmol.currentDocument){if(_jmol.isXHTML&&!_jmol.XhtmlElement){var s=document.getElementsByTagName("script")
_jmol.XhtmlElement=s.item(s.length-1)
_jmol.XhtmlAppendChild=false}
if(_jmol.XhtmlElement){_jmolDomDocumentWrite(text)}else{_jmol.currentDocument.write(text);}}
return text;}
function _jmolDomDocumentWrite(data){var pt=0
var Ptr=[]
Ptr[0]=0
while(Ptr[0]<data.length){var child=_jmolGetDomElement(data,Ptr)
if(!child)break
if(_jmol.XhtmlAppendChild)
_jmol.XhtmlElement.appendChild(child)
else
_jmol.XhtmlElement.parentNode.insertBefore(child,_jmol.XhtmlElement);}}
function _jmolGetDomElement(data,Ptr,closetag,lvel){var e=document.createElement("span")
e.innerHTML=data
Ptr[0]=data.length
return e
closetag||(closetag="")
lvel||(lvel=0)
var pt0=Ptr[0]
var pt=pt0
while(pt<data.length&&data.charAt(pt)!="<")pt++
if(pt!=pt0){var text=data.substring(pt0,pt)
Ptr[0]=pt
return document.createTextNode(text)}
pt0=++pt
var ch
while(pt<data.length&&"\n\r\t >".indexOf(ch=data.charAt(pt))<0)pt++
var tagname=data.substring(pt0,pt)
var e=(tagname==closetag||tagname=="/"?"":document.createElementNS?document.createElementNS('http://www.w3.org/1999/xhtml',tagname):document.createElement(tagname));if(ch==">"){Ptr[0]=++pt
return e}
while(pt<data.length&&(ch=data.charAt(pt))!=">"){while(pt<data.length&&"\n\r\t ".indexOf(ch=data.charAt(pt))>=0)pt++
pt0=pt
while(pt<data.length&&"\n\r\t =/>".indexOf(ch=data.charAt(pt))<0)pt++
var attrname=data.substring(pt0,pt).toLowerCase()
if(attrname&&ch!="=")
e.setAttribute(attrname,"true")
while(pt<data.length&&"\n\r\t ".indexOf(ch=data.charAt(pt))>=0)pt++
if(ch=="/"){Ptr[0]=pt+2
return e}else if(ch=="="){var quote=data.charAt(++pt)
pt0=++pt
while(pt<data.length&&(ch=data.charAt(pt))!=quote)pt++
var attrvalue=data.substring(pt0,pt)
e.setAttribute(attrname,attrvalue)
pt++}}
Ptr[0]=++pt
while(Ptr[0]<data.length){var child=_jmolGetDomElement(data,Ptr,"/"+tagname,lvel+1)
if(!child)break
e.appendChild(child)}
return e}
function _jmolPopup(url){var popup=window.open(url,"JmolPopup","left=150,top=150,height=400,width=600,"+"directories=yes,location=yes,menubar=yes,"+"toolbar=yes,"+"resizable=yes,scrollbars=yes,status=yes");if(popup.focus)
poup.focus();}
function _jmolReadyCallback(name){if(_jmol.debugAlert)
alert(name+" is ready");_jmol.ready[""+name]=true;}
function _jmolSterilizeScript(script){script=script.replace(/'/g,"&#39;");if(_jmol.debugAlert)
alert("script:\n"+script);return script;}
function _jmolSterilizeInline(model){model=model.replace(/\r|\n|\r\n/g,(model.indexOf("|")>=0?"\\/n":"|")).replace(/'/g,"&#39;");if(_jmol.debugAlert)
alert("inline model:\n"+model);return model;}
function _jmolRadio(script,labelHtml,isChecked,separatorHtml,groupName,id,title){++_jmol.radioCount;groupName!=undefined&&groupName!=null||(groupName="jmolRadioGroup"+(_jmol.radioGroupCount-1));if(!script)
return"";labelHtml!=undefined&&labelHtml!=null||(labelHtml=script.substring(0,32));separatorHtml||(separatorHtml="")
var scriptIndex=_jmolAddScript(script);var eospan="</span>"
var t="<span id=\"span_"+id+"\""+(title?" title=\""+title+"\"":"")+"><input name='"
+groupName+"' id='"+id+"' type='radio' onclick='_jmolClick(this,"+
scriptIndex+_jmol.targetText+");return true;' onmouseover='_jmolMouseOver("+
scriptIndex+");return true;' onmouseout='_jmolMouseOut()' "+
(isChecked?"checked='true' ":"")+_jmol.radioCssText+" />"
if(labelHtml.toLowerCase().indexOf("<td>")>=0){t+=eospan
eospan="";}
t+="<label for=\""+id+"\">"+labelHtml+"</label>"+eospan+separatorHtml;return t;}
function _jmolFindApplet(target){var applet=_jmolFindAppletInWindow(_jmol.appletWindow!=null?_jmol.appletWindow:window,target);if(applet==undefined)
applet=_jmolSearchFrames(window,target);if(applet==undefined)
applet=_jmolSearchFrames(top,target);return applet;}
function _jmolGetApplet(targetSuffix){var target="jmolApplet"+(targetSuffix?targetSuffix:"0");var applet=_jmolFindApplet(target);if(applet)return applet
_jmol.alerted||alert("could not find applet "+target);_jmol.alerted=true;return null}
function _jmolSearchFrames(win,target){var applet;var frames=win.frames;if(frames&&frames.length){try{for(var i=0;i<frames.length;++i){applet=_jmolSearchFrames(frames[i],target);if(applet)
return applet;}}catch(e){if(_jmol.debugAlert)
alert("Jmol.js _jmolSearchFrames cannot access "+win.name+".frame["+i+"] consider using jmolSetAppletWindow()")}}
return applet=_jmolFindAppletInWindow(win,target)}
function _jmolFindAppletInWindow(win,target){var doc=win.document;if(doc.getElementById(target))
return doc.getElementById(target);else if(doc.applets)
return doc.applets[target];else
return doc[target];}
function _jmolAddScript(script){if(!script)
return 0;var index=_jmol.scripts.length;_jmol.scripts[index]=script;return index;}
function _jmolClick(elementClicked,scriptIndex,targetSuffix){_jmol.element=elementClicked;_jmolScriptExecute(elementClicked,_jmol.scripts[scriptIndex],targetSuffix);}
function _jmolMenuSelected(menuObject,targetSuffix){var scriptIndex=menuObject.value;if(scriptIndex!=undefined){_jmolScriptExecute(menuObject,_jmol.scripts[scriptIndex],targetSuffix);return;}
var len=menuObject.length;if(typeof len=="number"){for(var i=0;i<len;++i){if(menuObject[i].selected){_jmolClick(menuObject[i],menuObject[i].value,targetSuffix);return;}}}
alert("?Que? menu selected bug #8734");}
_jmol.checkboxMasters={};_jmol.checkboxItems={};function jmolSetCheckboxGroup(chkMaster,chkBox){var id=chkMaster;if(typeof(id)=="number")id="jmolCheckbox"+id;chkMaster=document.getElementById(id);if(!chkMaster)alert("jmolSetCheckboxGroup: master checkbox not found: "+id);var m=_jmol.checkboxMasters[id]={};m.chkMaster=chkMaster;m.chkGroup={};for(var i=1;i<arguments.length;i++){var id=arguments[i];if(typeof(id)=="number")id="jmolCheckbox"+id;checkboxItem=document.getElementById(id);if(!checkboxItem)alert("jmolSetCheckboxGroup: group checkbox not found: "+id);m.chkGroup[id]=checkboxItem;_jmol.checkboxItems[id]=m;}}
function _jmolNotifyMaster(m){var allOn=true;var allOff=true;for(var chkBox in m.chkGroup){if(m.chkGroup[chkBox].checked)
allOff=false;else
allOn=false;}
if(allOn)m.chkMaster.checked=true;if(allOff)m.chkMaster.checked=false;if((allOn||allOff)&&_jmol.checkboxItems[m.chkMaster.id])
_jmolNotifyMaster(_jmol.checkboxItems[m.chkMaster.id])}
function _jmolNotifyGroup(m,isOn){for(var chkBox in m.chkGroup){var item=m.chkGroup[chkBox]
item.checked=isOn;if(_jmol.checkboxMasters[item.id])
_jmolNotifyGroup(_jmol.checkboxMasters[item.id],isOn)}}
function _jmolCbClick(ckbox,whenChecked,whenUnchecked,targetSuffix){_jmol.control=ckbox
_jmolClick(ckbox,ckbox.checked?whenChecked:whenUnchecked,targetSuffix);if(_jmol.checkboxMasters[ckbox.id])
_jmolNotifyGroup(_jmol.checkboxMasters[ckbox.id],ckbox.checked)
if(_jmol.checkboxItems[ckbox.id])
_jmolNotifyMaster(_jmol.checkboxItems[ckbox.id])}
function _jmolCbOver(ckbox,whenChecked,whenUnchecked){window.status=_jmol.scripts[ckbox.checked?whenUnchecked:whenChecked];}
function _jmolMouseOver(scriptIndex){window.status=_jmol.scripts[scriptIndex];}
function _jmolMouseOut(){window.status=" ";return true;}
function _jmolSetCodebase(codebase){_jmol.codebase=codebase?codebase:".";if(_jmol.debugAlert)
alert("jmolCodebase="+_jmol.codebase);}
function _jmolOnloadResetForms(){_jmol.previousOnloadHandler=window.onload;window.onload=function(){with(_jmol){if(buttonCount+checkboxCount+menuCount+radioCount+radioGroupCount>0){var forms=document.forms;for(var i=forms.length;--i>=0;)
forms[i].reset();}
if(previousOnloadHandler)
previousOnloadHandler();}}}
function _jmolEvalJSON(s,key){s=s+""
if(!s)return[]
if(s.charAt(0)!="{"){if(s.indexOf(" | ")>=0)s=s.replace(/\ \|\ /g,"\n")
return s}
var A=eval("("+s+")")
if(!A)return
if(key&&A[key])A=A[key]
return A}
function _jmolEnumerateObject(A,key){var sout=""
if(typeof(A)=="string"&&A!="null"){sout+="\n"+key+"=\""+A+"\""}else if(!isNaN(A)||A==null){sout+="\n"+key+"="+(A+""==""?"null":A)}else if(A.length){sout+=key+"=[]"
for(var i=0;i<A.length;i++){sout+="\n"
if(typeof(A[i])=="object"||typeof(A[i])=="array"){sout+=_jmolEnumerateObject(A[i],key+"["+i+"]")}else{sout+=key+"["+i+"]="+(typeof(A[i])=="string"&&A[i]!="null"?"\""+A[i].replace(/\"/g,"\\\"")+"\"":A[i])}}}else{if(key!=""){sout+=key+"={}"
key+="."}
for(var i in A){sout+="\n"
if(typeof(A[i])=="object"||typeof(A[i])=="array"){sout+=_jmolEnumerateObject(A[i],key+i)}else{sout+=key+i+"="+(typeof(A[i])=="string"&&A[i]!="null"?"\""+A[i].replace(/\"/g,"\\\"")+"\"":A[i])}}}
return sout}
function _jmolSortKey0(a,b){return(a[0]<b[0]?1:a[0]>b[0]?-1:0)}
function _jmolSortMessages(A){if(!A||typeof(A)!="object")return[]
var B=[]
for(var i=A.length-1;i>=0;i--)for(var j=0;j<A[i].length;j++)B[B.length]=A[i][j]
if(B.length==0)return
B=B.sort(_jmolSortKey0)
return B}
function _jmolDomScriptLoad(URL){_jmol.servercall=URL
var node=document.getElementById("_jmolScriptNode")
if(node&&_jmol.browser!="msie"){document.getElementsByTagName("HEAD")[0].removeChild(node)
node=null}
if(node){node.setAttribute("src",URL)}else{node=document.createElement("script")
node.setAttribute("id","_jmolScriptNode")
node.setAttribute("type","text/javascript")
node.setAttribute("src",URL)
document.getElementsByTagName("HEAD")[0].appendChild(node)}}
function _jmolExtractPostData(url){S=url.split("&POST:")
var s=""
for(var i=1;i<S.length;i++){KV=S[i].split("=")
s+="&POSTKEY"+i+"="+KV[0]
s+="&POSTVALUE"+i+"="+KV[1]}
return"&url="+escape(S[0])+s}
function _jmolLoadModel(targetSuffix,remoteURL,array,isError,errorMessage){_jmol.remoteURL=remoteURL
isError&&alert(errorMessage)
jmolLoadInlineScript(array.join("\n"),_jmol.optionalscript,targetSuffix)}
function jmolGetStatus(strStatus,targetSuffix){return _jmolSortMessages(jmolGetPropertyAsArray("jmolStatus",strStatus,targetSuffix))}
function jmolGetPropertyAsArray(sKey,sValue,targetSuffix){return _jmolEvalJSON(jmolGetPropertyAsJSON(sKey,sValue,targetSuffix),sKey)}
function jmolGetPropertyAsString(sKey,sValue,targetSuffix){var applet=_jmolGetApplet(targetSuffix);sValue==undefined&&(sValue="");return(applet?applet.getPropertyAsString(sKey,sValue)+"":"")}
function jmolGetPropertyAsJSON(sKey,sValue,targetSuffix){sValue==undefined&&(sValue="")
var applet=_jmolGetApplet(targetSuffix);try{return(applet?applet.getPropertyAsJSON(sKey,sValue)+"":"")}catch(e){return""}}
function jmolGetPropertyAsJavaObject(sKey,sValue,targetSuffix){sValue==undefined&&(sValue="")
var applet=_jmolGetApplet(targetSuffix);return(applet?applet.getProperty(sKey,sValue):null)}
function jmolDecodeJSON(s){return _jmolEnumerateObject(_jmolEvalJSON(s),"")}
function jmolScriptWait(script,targetSuffix){targetSuffix==undefined&&(targetSuffix="0")
var Ret=jmolScriptWaitAsArray(script,targetSuffix)
var s=""
for(var i=Ret.length;--i>=0;)
for(var j=0;j<Ret[i].length;j++)
s+=Ret[i][j]+"\n"
return s}
function jmolScriptWaitOutput(script,targetSuffix){targetSuffix==undefined&&(targetSuffix="0")
var ret=""
try{if(script){_jmolCheckBrowser();var applet=_jmolGetApplet(targetSuffix);if(applet)ret+=applet.scriptWaitOutput(script);}}catch(e){}
return ret;}
function jmolEvaluate(molecularMath,targetSuffix){targetSuffix==undefined&&(targetSuffix="0")
var result=""+jmolGetPropertyAsJavaObject("evaluate",molecularMath,targetSuffix);var s=result.replace(/\-*\d+/,"")
if(s==""&&!isNaN(parseInt(result)))return parseInt(result);var s=result.replace(/\-*\d*\.\d*/,"")
if(s==""&&!isNaN(parseFloat(result)))return parseFloat(result);return result;}
function jmolScriptEcho(script,targetSuffix){targetSuffix==undefined&&(targetSuffix="0")
var Ret=jmolScriptWaitAsArray(script,targetSuffix)
var s=""
for(var i=Ret.length;--i>=0;)
for(var j=Ret[i].length;--j>=0;)
if(Ret[i][j][1]=="scriptEcho")s+=Ret[i][j][3]+"\n"
return s.replace(/ \| /g,"\n")}
function jmolScriptMessage(script,targetSuffix){targetSuffix==undefined&&(targetSuffix="0")
var Ret=jmolScriptWaitAsArray(script,targetSuffix)
var s=""
for(var i=Ret.length;--i>=0;)
for(var j=Ret[i].length;--j>=0;)
if(Ret[i][j][1]=="scriptStatus")s+=Ret[i][j][3]+"\n"
return s.replace(/ \| /g,"\n")}
function jmolScriptWaitAsArray(script,targetSuffix){var ret=""
try{jmolGetStatus("scriptEcho,scriptMessage,scriptStatus,scriptError",targetSuffix)
if(script){_jmolCheckBrowser();var applet=_jmolGetApplet(targetSuffix);if(applet)ret+=applet.scriptWait(script);ret=_jmolEvalJSON(ret,"jmolStatus")
if(typeof ret=="object")
return ret}}catch(e){}
return[[ret]]}
function jmolSaveOrientation(id,targetSuffix){targetSuffix==undefined&&(targetSuffix="0")
return _jmol["savedOrientation"+id]=jmolGetPropertyAsArray("orientationInfo","info",targetSuffix).moveTo}
function jmolRestoreOrientation(id,targetSuffix){targetSuffix==undefined&&(targetSuffix="0")
var s=_jmol["savedOrientation"+id]
if(!s||s=="")return
s=s.replace(/1\.0/,"0")
return jmolScriptWait(s,targetSuffix)}
function jmolRestoreOrientationDelayed(id,delay,targetSuffix){arguments.length<2&&(delay=1)
targetSuffix==undefined&&(targetSuffix="0")
var s=_jmol["savedOrientation"+id]
if(!s||s=="")return
s=s.replace(/1\.0/,delay)
return jmolScriptWait(s,targetSuffix)}
function jmolAppletAddParam(appletCode,name,value){return(value==""?appletCode:appletCode.replace(/\<param/,"\n<param name='"+name+"' value='"+value+"' />\n<param"))}
function jmolLoadAjax_STOLAF_RCSB(fileformat,pdbid,optionalscript,targetSuffix){_jmol.thismodel||(_jmol.thismodel="1crn")
_jmol.serverURL||(_jmol.serverURL="http://fusion.stolaf.edu/chemistry/jmol/getajaxjs.cfm")
_jmol.RCSBserver||(_jmol.RCSBserver="http://www.rcsb.org")
_jmol.defaultURL_RCSB||(_jmol.defaultURL_RCSB=_jmol.RCSBserver+"/pdb/files/1CRN.CIF")
fileformat||(fileformat="PDB")
pdbid||(pdbid=prompt("Enter a 4-digit PDB ID:",_jmol.thismodel))
if(!pdbid||pdbid.length!=4)return""
targetSuffix||(targetSuffix="0")
optionalscript||(optionalscript="")
var url=_jmol.defaultURL_RCSB.replace(/1CRN/g,pdbid.toUpperCase())
fileformat=="CIF"||(url=url.replace(/CIF/,fileformat))
_jmol.optionalscript=optionalscript
_jmol.thismodel=pdbid
_jmol.thistargetsuffix=targetSuffix
_jmol.thisurl=url
_jmol.modelArray=[]
url=_jmol.serverURL+"?returnfunction=_jmolLoadModel&returnArray=_jmol.modelArray&id="+targetSuffix+_jmolExtractPostData(url)
_jmolDomScriptLoad(url)
return url}
function jmolLoadAjax_STOLAF_ANY(url,userid,optionalscript,targetSuffix){_jmol.serverURL="http://fusion.stolaf.edu/chemistry/jmol/getajaxjs.cfm"
_jmol.thisurlANY||(_jmol.thisurlANY="http://www.stolaf.edu/depts/chemistry/mo/struc/data/ycp3-1.mol")
url||(url=prompt("Enter any (uncompressed file) URL:",_jmol.thisurlANY))
userid||(userid="0")
targetSuffix||(targetSuffix="0")
optionalscript||(optionalscript="")
_jmol.optionalscript=optionalscript
_jmol.thistargetsuffix=targetSuffix
_jmol.modelArray=[]
_jmol.thisurl=url
url=_jmol.serverURL+"?returnfunction=_jmolLoadModel&returnArray=_jmol.modelArray&id="+targetSuffix+_jmolExtractPostData(url)
_jmolDomScriptLoad(url)}
function jmolLoadAjax_MSA(key,value,optionalscript,targetSuffix){_jmol.thiskeyMSA||(_jmol.thiskeyMSA="mineral")
_jmol.thismodelMSA||(_jmol.thismodelMSA="quartz")
_jmol.ajaxURL_MSA||(_jmol.ajaxURL_MSA="http://rruff.geo.arizona.edu/AMS/result.php?mineral=quartz&viewing=ajaxjs")
key||(key=prompt("Enter a field:",_jmol.thiskeyMSA))
if(!key)return""
value||(value=prompt("Enter a "+key+":",_jmol.thismodelMSA))
if(!value)return""
targetSuffix||(targetSuffix="0")
optionalscript||(optionalscript="")
optionalscript==1&&(optionalscript='load "" {1 1 1}')
var url=_jmol.ajaxURL_MSA.replace(/mineral/g,key).replace(/quartz/g,value)
_jmol.optionalscript=optionalscript
_jmol.thiskeyMSA=key
_jmol.thismodelMSA=value
_jmol.thistargetsuffix=targetSuffix
_jmol.thisurl=url
_jmol.modelArray=[]
loadModel=_jmolLoadModel
_jmolDomScriptLoad(url)
return url}
function jmolLoadAjaxJS(url,userid,optionalscript,targetSuffix){userid||(userid="0")
targetSuffix||(targetSuffix="0")
optionalscript||(optionalscript="")
_jmol.optionalscript=optionalscript
_jmol.thismodel=userid
_jmol.thistargetsuffix=targetSuffix
_jmol.modelArray=[]
_jmol.thisurl=url
url+="&returnFunction=_jmolLoadModel&returnArray=_jmol.modelArray&id="+targetSuffix
_jmolDomScriptLoad(url)}}catch(e){}
function jmolSetAtomCoord(i,x,y,z,targetSuffix){_jmolCheckBrowser();var applet=_jmolGetApplet(targetSuffix);if(applet)applet.getProperty('jmolViewer').setAtomCoord(i,x,y,z)}
function jmolSetAtomCoordRelative(i,x,y,z,targetSuffix){_jmolCheckBrowser();var applet=_jmolGetApplet(targetSuffix);if(applet)applet.getProperty('jmolViewer').setAtomCoordRelative(i,x,y,z)}
if(_jmol.useNoApplet){jmolApplet=function(w){var s="<table style='background-color:black' width="+w+"><tr height="+w+">"
+"<td align=center valign=center style='background-color:white'>"
+"Applet would be here"
+"<p><textarea id=fakeApplet rows=5 cols=50></textarea>"
+"</td></tr></table>"
return _jmolDocumentWrite(s)}
_jmolFindApplet=function(){return jmolApplet0}
jmolApplet0={script:function(script){document.getElementById("fakeApplet").value="\njmolScript:\n"+script},scriptWait:function(script){document.getElementById("fakeApplet").value="\njmolScriptWait:\n"+script},loadInline:function(data,script){document.getElementById("fakeApplet").value="\njmolLoadInline data:\n"+data+"\n\nscript:\n"+script}}}
function jmolResize(w,h,targetSuffix){_jmol.alerted=true;var percentW=(!w?100:w<=1&&w>0?w*100:0);var percentH=(!h?percentW:h<=1&&h>0?h*100:0);if(_jmol.browser=="msie"){var width=document.body.clientWidth;var height=document.body.clientHeight;}else{var netscapeScrollWidth=15;var width=window.innerWidth-netscapeScrollWidth;var height=window.innerHeight-netscapeScrollWidth;}
var applet=_jmolGetApplet(targetSuffix);if(!applet)return;applet.style.width=(percentW?width*percentW/100:w)+"px";applet.style.height=(percentH?height*percentH/100:(h?h:w))+"px";}
function jmolResizeApplet(size,targetSuffix){_jmol.alerted=true;var applet=_jmolGetApplet(targetSuffix);if(!applet)return;var sz=_jmolGetAppletSize(size,"px");sz[0]&&(applet.style.width=sz[0]);sz[1]&&(applet.style.height=sz[1]);}
function _jmolGetAppletSize(size,units){var width,height;if((typeof size)=="object"&&size!=null){width=size[0];height=size[1];}else{width=height=size;}
return[_jmolFixDim(width,units),_jmolFixDim(height,units)];}
function _jmolFixDim(x,units){var sx=""+x;return(sx.length==0?(units?"":_jmol.allowedJmolSize[2]):sx.indexOf("%")==sx.length-1?sx:(x=parseFloat(x))<=1&&x>0?x*100+"%":(isNaN(x=Math.floor(x))?_jmol.allowedJmolSize[2]:x<_jmol.allowedJmolSize[0]?_jmol.allowedJmolSize[0]:x>_jmol.allowedJmolSize[1]?_jmol.allowedJmolSize[1]:x)+(units?units:""));}