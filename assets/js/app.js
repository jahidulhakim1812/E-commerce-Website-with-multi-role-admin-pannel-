(function(){
var $=function(s,c){return(c||document).querySelector(s)},$$=function(s,c){return Array.prototype.slice.call((c||document).querySelectorAll(s))};
var csrf=($('meta[name=csrf]')||{}).content||'',B=window.BASE||'';
function esc(s){var d=document.createElement('div');d.textContent=s==null?'':s;return d.innerHTML}
function api(a,d){var f=new FormData();f.append('a',a);for(var k in(d||{}))f.append(k,d[k]);return fetch(B+'/api.php',{method:'POST',body:f,headers:{'X-CSRF':csrf}}).then(function(r){return r.json()})}
function toast(m){var t=$('#toast');if(!t||!m)return;t.textContent=m;t.classList.add('show');clearTimeout(t._t);t._t=setTimeout(function(){t.classList.remove('show')},2200)}
function setCount(n){$$('.js-cnt').forEach(function(c){c.textContent=n});$$('.js-cnt2').forEach(function(c){c.textContent=n})}
/* theme */
$$('[data-theme-toggle]').forEach(function(b){b.addEventListener('click',function(){var d=document.documentElement,n=d.getAttribute('data-theme')==='dark'?'light':'dark';d.setAttribute('data-theme',n);localStorage.setItem('theme',n)})});
/* drawers: menu = left, cart = right */
var ov=$('#overlay'),md=$('#menuDrawer'),cd=$('#cartDrawer');
function closeAll(){$$('.drawer').forEach(function(d){d.classList.remove('open')});ov.classList.remove('show');document.body.style.overflow=''}
function openD(el){closeAll();el.classList.add('open');ov.classList.add('show');document.body.style.overflow='hidden'}
if($('#menuBtn'))$('#menuBtn').onclick=function(){openD(md)};
$$('[data-close]').forEach(function(b){b.onclick=closeAll});if(ov)ov.onclick=closeAll;document.addEventListener('keydown',function(e){if(e.key==='Escape')closeAll()});
$$('.d-tg').forEach(function(b){b.onclick=function(){b.parentNode.classList.toggle('open')}});
function loadCart(open){return api('mini').then(function(r){if(!r.ok)return;setCount(r.count);
 $('#cdTitle').textContent=T.cart+' ('+r.count+')';$$('.js-tot').forEach(function(x){x.textContent=r.sub});$('#cdSub').textContent=r.sub;$('#cdTot').textContent=r.total;
 var fr=$('#cdFree');fr.innerHTML=(r.count&&r.free)?(r.need?IC.truck+' '+esc(T.free)+' <b>'+esc(r.need)+'</b> '+esc(T.free2)+'<div class="bar"><i style="width:'+r.pct+'%"></i></div>':'<b>'+esc(T.free3)+'</b><div class="bar"><i style="width:100%"></i></div>'):'';
 var h='';if(!r.lines.length)h='<div class="cd-empty"><span>'+IC.cart+'</span>'+esc(T.empty)+'</div>';
 r.lines.forEach(function(l){h+='<div class="cd-line" data-key="'+esc(l.key)+'"><a class="ci" href="'+esc(l.url)+'">'+(l.img?'<img src="'+esc(l.img)+'" alt="">':'<span class="ph3d">'+esc(l.icon)+'</span>')+'</a><div><a href="'+esc(l.url)+'"><b>'+esc(l.name)+'</b></a>'+(l.label?' <span class="vtag">'+esc(l.label)+'</span>':'')+'<small>'+esc(l.price)+'</small><div class="qty"><button type="button" data-dq="-1">−</button><input type="number" value="'+l.qty+'" min="1" max="'+l.max+'" data-dqin><button type="button" data-dq="1">+</button></div></div><div><button class="cd-rm" data-drm aria-label="remove">'+IC.x+'</button><div class="cd-lt">'+esc(l.line)+'</div></div></div>'});
 $('#cdBody').innerHTML=h;if(open)openD(cd)})}
$$('[data-open-cart]').forEach(function(b){b.onclick=function(){loadCart(true)}});
document.addEventListener('click',function(e){var l=e.target.closest('.cd-line');if(!l)return;var inp=$('[data-dqin]',l),key=l.dataset.key,q;
 var q1=e.target.closest('[data-dq]');if(q1){q=Math.min(+inp.max||99,Math.max(1,(+inp.value||1)+ +q1.dataset.dq))}else if(e.target.closest('[data-drm]')){q=0}else return;
 api('set',{key:key,qty:q}).then(function(){loadCart(false)})});
document.addEventListener('change',function(e){if(e.target.matches('[data-dqin]')){var l=e.target.closest('.cd-line');api('set',{key:l.dataset.key,qty:Math.max(1,+e.target.value||1)}).then(function(){loadCart(false)})}});
/* floating contact + to top */
if($('#fcBtn'))$('#fcBtn').onclick=function(){$('#fc').classList.toggle('open')};
var tt=$('#toTop');window.addEventListener('scroll',function(){tt&&tt.classList.toggle('show',scrollY>400)});
/* topbar ticker on mobile */
var tbs=$$('.tb-i');if(tbs.length){var k=0;var tick=function(){if(innerWidth<=760){tbs.forEach(function(x,i){x.classList.toggle('show',i===k)});k=(k+1)%tbs.length}else tbs.forEach(function(x){x.classList.remove('show')})};tick();setInterval(tick,3000)}
/* hero slider */
var sl=$('.slides');if(sl){var ss=$$('.slide',sl),ds=$$('.dots i',sl),i=0,tm;var fitSlideImage=function(s){if(!sl.classList.contains('slides-images'))return;var img=$('.sf-img',s);if(img&&img.naturalWidth&&img.naturalHeight){var ratio=img.naturalWidth+' / '+img.naturalHeight;sl.style.aspectRatio=ratio;sl.style.setProperty('--slide-image-ratio',ratio)}};var go=function(n){i=(n+ss.length)%ss.length;ss.forEach(function(s,k){s.classList.toggle('active',k===i);if(k===i)fitSlideImage(s)});ds.forEach(function(d,k){d.classList.toggle('on',k===i)})};ss.forEach(function(s){var img=$('.sf-img',s);if(img)img.addEventListener('load',function(){if(s.classList.contains('active'))fitSlideImage(s)})});if(ss.length)fitSlideImage(ss[0]);var auto=function(){clearInterval(tm);if(ss.length>1)tm=setInterval(function(){go(i+1)},5500)};
 var p=$('.arrow.prev',sl),n=$('.arrow.next',sl);if(p){p.onclick=function(){go(i-1);auto()};n.onclick=function(){go(i+1);auto()}}ds.forEach(function(d,k){d.onclick=function(){go(k);auto()}});auto();
 var sx=0;sl.addEventListener('touchstart',function(e){sx=e.touches[0].clientX},{passive:true});sl.addEventListener('touchend',function(e){var dx=e.changedTouches[0].clientX-sx;if(Math.abs(dx)>50){go(i+(dx<0?1:-1));auto()}})}
/* horizontal sliders: data-auto=1 slides one card at a time, forever (categories, testimonials) */
var reduce=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
$$('.hs').forEach(function(h){var t=$('.hs-track',h),auto=h.dataset.auto==='1',paused=false,half=0,step=0,rs;
 var pb=$('.hs-btn.prev',h),nb=$('.hs-btn.next',h);
 function fix(){if(half&&t.scrollLeft>=half-2){t.style.scrollBehavior='auto';t.scrollLeft-=half;t.style.scrollBehavior=''}}
 if(auto){var base=$$('.hs-track>*',h),n=base.length;if(n>1){var addSet=function(){base.forEach(function(c){var cl=c.cloneNode(true);cl.setAttribute('aria-hidden','true');cl.tabIndex=-1;t.appendChild(cl)})};addSet();half=t.children[n].offsetLeft-t.children[0].offsetLeft;var g=0;while(t.scrollWidth<half+t.clientWidth+400&&g++<6)addSet();step=t.children[1].offsetLeft-t.children[0].offsetLeft}else auto=false}
 function slide(d){if(!step)step=Math.max(160,t.clientWidth*.7);if(d<0&&half&&t.scrollLeft<step){t.style.scrollBehavior='auto';t.scrollLeft+=half;t.style.scrollBehavior=''}t.scrollBy({left:d*step,behavior:'smooth'});clearTimeout(t._f);t._f=setTimeout(fix,700)}
 if(pb){pb.onclick=function(){slide(-1)};nb.onclick=function(){slide(1)}}
 if(auto&&!reduce){setInterval(function(){if(!paused&&!document.hidden)slide(1)},2600)}
 ['mouseenter','touchstart','focusin'].forEach(function(e){h.addEventListener(e,function(){paused=true;clearTimeout(rs)},{passive:true})});['mouseleave','touchend','focusout'].forEach(function(e){h.addEventListener(e,function(){clearTimeout(rs);rs=setTimeout(function(){paused=false},2200)},{passive:true})});
 t.addEventListener('scroll',function(){clearTimeout(t._f);t._f=setTimeout(fix,160)},{passive:true})});
/* notifications bell: site alerts for every visitor (new products, offers, admin messages) */
(function(){var bell=$('#bellBtn');if(!bell)return;var pop=$('#nfPop'),list=$('#nfList'),cnt=$('#nfCnt'),KEY='dn_seen',seen=+localStorage.getItem(KEY)||0,known=-1,items=[],max=0,pushActive=false;
 var ico={product:IC.tag,offer:IC.gift,message:IC.megaphone},lab={product:T.nprod,offer:T.noffer,message:T.nmsg};
 function render(){var un=items.filter(function(i){return i.id>seen}).length;cnt.hidden=!un;cnt.textContent=un>9?'9+':un;
  list.innerHTML=items.length?items.map(function(i){var tag=i.url?'a href="'+esc(i.url)+'"':'div';var end=i.url?'a':'div';return '<'+tag+' class="nf-i'+(i.id>seen?' new':'')+'"><span class="nf-ic">'+(i.img?'<img src="'+esc(i.img)+'" alt="">':ico[i.type]||IC.megaphone)+'</span><span class="nf-t"><em>'+esc(lab[i.type]||'')+'</em><b>'+esc(i.title)+'</b><small>'+esc(i.body)+'</small><i>'+esc(i.date)+'</i></span></'+end+'>'}).join(''):'<p class="nf-empty">'+esc(T.nonew)+'</p>'}
 function load(){return api('notifs').then(function(r){if(!r.ok)return;items=r.items;max=r.max;
   if(known>=0&&!pushActive&&'Notification' in window&&Notification.permission==='granted')items.filter(function(i){return i.id>known&&i.id>seen}).forEach(function(i){try{var nn=new Notification(i.title,{body:i.body});nn.onclick=function(){if(i.url)window.open(i.url,'_blank')}}catch(e){console.error('Could not show browser notification',e)}});
   known=max;render()})}
 function markSeen(){seen=max;localStorage.setItem(KEY,seen);cnt.hidden=true}
 bell.onclick=function(e){e.stopPropagation();var o=pop.classList.toggle('open');if(o){setTimeout(function(){markSeen();$$('.nf-i.new',list).forEach(function(x){setTimeout(function(){x.classList.remove('new')},2500)})},1200)}};
 document.addEventListener('click',function(e){if(!e.target.closest('.nf'))pop.classList.remove('open')});
 $('#nfAll').onclick=function(){markSeen();render()};
 var pm=$('#nfPerm'),pushHelp=$('#nfPushHelp');
 function base64Key(s){var pad='='.repeat((4-s.length%4)%4);var raw=atob((s+pad).replace(/-/g,'+').replace(/_/g,'/'));var out=new Uint8Array(raw.length);for(var i=0;i<raw.length;i++)out[i]=raw.charCodeAt(i);return out}
 function pushButtonState(){if(!pm)return;if(!window.PUSH_ENABLED||!window.PUSH_PUBLIC_KEY||!('serviceWorker'in navigator)||!('PushManager'in window)||!('Notification'in window)||Notification.permission==='denied'){pm.hidden=true;if(pushHelp)pushHelp.hidden=true;return}
   pm.hidden=false;pm.textContent=pushActive?T.disablePush:T.enablePush;if(pushHelp)pushHelp.hidden=pushActive;
 }
 function registerPush(){if(!('serviceWorker'in navigator))return Promise.reject(new Error('This browser does not support background notifications.'));
   return navigator.serviceWorker.register(B+'/sw.js',{scope:B+'/'}).then(function(){return navigator.serviceWorker.ready})}
 function setupPush(){if(!pm||!window.PUSH_ENABLED||!window.PUSH_PUBLIC_KEY||!('serviceWorker'in navigator)||!('PushManager'in window)||!('Notification'in window)){pushButtonState();return}
   registerPush().then(function(reg){return reg.pushManager.getSubscription()}).then(function(sub){if(sub){pushActive=true;api('push-subscribe',{subscription:JSON.stringify(sub.toJSON())}).catch(function(err){console.error('Could not refresh push subscription',err)})}pushButtonState()}).catch(function(err){console.error('Could not register push service worker',err);pushButtonState()});
   pm.onclick=function(){if(Notification.permission==='denied'){toast('Notifications are blocked in your browser settings.');return}
     if(pushActive){registerPush().then(function(reg){return reg.pushManager.getSubscription()}).then(function(sub){if(!sub){pushActive=false;pushButtonState();return}
       return api('push-unsubscribe',{subscription:JSON.stringify(sub.toJSON())}).then(function(r){if(!r.ok)throw new Error(r.msg||'Could not turn off notifications.');return sub.unsubscribe()}).then(function(unsubscribed){if(!unsubscribed)throw new Error('The browser could not remove this subscription.');pushActive=false;pushButtonState();toast('Push notifications turned off.')})
     }).catch(function(err){console.error('Could not disable push notifications',err);toast(err.message||'Could not turn off notifications.')});return}
     var permission=Notification.permission==='granted'?Promise.resolve('granted'):Notification.requestPermission();
     permission.then(function(result){if(result!=='granted')throw new Error('Notification permission was not granted.');return registerPush()}).then(function(reg){return reg.pushManager.getSubscription().then(function(sub){if(sub)return {reg:reg,sub:sub};return reg.pushManager.subscribe({userVisibleOnly:true,applicationServerKey:base64Key(window.PUSH_PUBLIC_KEY)}).then(function(created){return {reg:reg,sub:created}})})}).then(function(data){return api('push-subscribe',{subscription:JSON.stringify(data.sub.toJSON())}).then(function(r){if(!r.ok)throw new Error(r.msg||'Could not enable notifications.');pushActive=true;pushButtonState();toast('Push notifications enabled.')})}).catch(function(err){console.error('Could not enable push notifications',err);toast(err.message||'Could not enable notifications. Check browser permission and HTTPS.')})}
 }
 pushButtonState();setupPush();
 var nf=$('#nfForm');if(nf)nf.onsubmit=function(e){e.preventDefault();api('newsletter',{email:nf.email.value}).then(function(r){toast(r.msg);if(r.ok)nf.reset()})};
 load();setInterval(function(){if(!document.hidden)load()},90000)})();
/* 3D tilt + glare */
if(window.matchMedia('(hover:hover)').matches&&!reduce){document.addEventListener('mousemove',function(e){var c=e.target.closest&&e.target.closest('.card3d');if(!c)return;var r=c.getBoundingClientRect(),x=(e.clientX-r.left)/r.width,y=(e.clientY-r.top)/r.height;c.style.transform='perspective(900px) rotateY('+((x-.5)*10)+'deg) rotateX('+(-(y-.5)*10)+'deg) translateZ(8px)';c.style.setProperty('--mx',x*100+'%');c.style.setProperty('--my',y*100+'%')});document.addEventListener('mouseout',function(e){var c=e.target.closest&&e.target.closest('.card3d');if(c&&!c.contains(e.relatedTarget))c.style.transform=''})}
/* add to cart / wishlist */
document.addEventListener('click',function(e){
 var a=e.target.closest('[data-add]');if(a){api('add',{id:a.dataset.add,qty:1}).then(function(r){if(r.ok){setCount(r.count);loadCart(true)}else toast(r.msg||'')});return}
 var w=e.target.closest('[data-wish]');if(w){api('wish',{id:w.dataset.wish}).then(function(r){w.classList.toggle('on',r.on)});return}
});
var nf=$('#newsForm');if(nf)nf.onsubmit=function(e){e.preventDefault();api('newsletter',{email:nf.email.value}).then(function(r){toast(r.msg);if(r.ok)nf.reset()})};
/* product page: weight options, live price, zoom + lightbox */
$$('[data-tab]').forEach(function(b){b.onclick=function(){$$('[data-tab]').forEach(function(x){x.classList.remove('on')});$$('.tab-c').forEach(function(x){x.classList.remove('on')});b.classList.add('on');$('#'+b.dataset.tab).classList.add('on')}});
var PVd=window.PV,q=$('#qty'),selVid=0;
function cur(n){return (PVd&&PVd.cur||'')+Math.round(n).toLocaleString('en-US')}
function applyVar(v){if(!v)return;selVid=v.id;$('#pdPrice').textContent=cur(v.price);var o=$('#pdOld'),pc=$('#pdPct');if(v.old>v.price){o.hidden=false;o.textContent=cur(v.old);pc.hidden=false;pc.textContent=PVd.tx.save.replace('0',v.pct)}else{o.hidden=true;pc.hidden=true}
 $('#pdStock').innerHTML=v.stock>0?'<span class="ok-t">● '+esc(PVd.tx.in)+(v.stock<=PVd.low?' — '+esc(PVd.tx.left.replace('0',v.stock)):'')+'</span>':'<span class="bad-t">● '+esc(PVd.tx.out)+'</span>';
 if(q){q.max=Math.max(1,v.stock);if(+q.value>v.stock)q.value=Math.max(1,v.stock)}[$('#addBtn'),$('#buyBtn')].forEach(function(b){if(b)b.disabled=v.stock<1})}
if(PVd&&PVd.vars&&PVd.vars.length){var on=$('.vchip.on');if(on)applyVar(PVd.vars.filter(function(v){return v.id==on.dataset.vid})[0]);
 $$('.vchip').forEach(function(b){b.onclick=function(){if(b.disabled)return;$$('.vchip').forEach(function(x){x.classList.remove('on')});b.classList.add('on');applyVar(PVd.vars.filter(function(v){return v.id==b.dataset.vid})[0])}})}
if(q){$$('[data-q]').forEach(function(b){b.onclick=function(){q.value=Math.min(+q.max||99,Math.max(1,(+q.value||1)+ +b.dataset.q))}});
 $('#addBtn').onclick=function(){api('add',{id:this.dataset.pid,vid:selVid,qty:q.value}).then(function(r){if(r.ok){setCount(r.count);loadCart(true)}else toast(r.msg)})};
 $('#buyBtn').onclick=function(){api('add',{id:this.dataset.pid,vid:selVid,qty:q.value}).then(function(r){if(r.ok)location.href=B+'/checkout.php';else toast(r.msg)})}}
var zw=$('#zoomWrap'),mi=$('#mainImg'),gi=0,lb=null;
function lbShow(i){var g=PVd.gal;if(!g.length)return;gi=(i+g.length)%g.length;var im=$('.lb-img',lb);im.classList.remove('z');im.style.transformOrigin='';im.src=g[gi];$('.lb-count',lb).textContent=(gi+1)+' / '+g.length;lb.classList.toggle('single',g.length<2)}
function openLb(){if(!PVd||!PVd.gal||!PVd.gal.length)return;if(!lb){lb=document.createElement('div');lb.className='lb';lb.innerHTML='<button class="lb-x" aria-label="close">'+IC.x+'</button><button class="lb-p" aria-label="prev">'+IC.prev+'</button><button class="lb-n" aria-label="next">'+IC.next+'</button><img class="lb-img" alt=""><div class="lb-count"></div>';document.body.appendChild(lb);
  $('.lb-x',lb).onclick=function(){lb.classList.remove('open');document.body.style.overflow=''};$('.lb-p',lb).onclick=function(){lbShow(gi-1)};$('.lb-n',lb).onclick=function(){lbShow(gi+1)};
  lb.addEventListener('click',function(e){if(e.target===lb){lb.classList.remove('open');document.body.style.overflow=''}});
  $('.lb-img',lb).onclick=function(e){var im=e.target,r=im.getBoundingClientRect();im.style.transformOrigin=((e.clientX-r.left)/r.width*100)+'% '+((e.clientY-r.top)/r.height*100)+'%';im.classList.toggle('z')};
  document.addEventListener('keydown',function(e){if(!lb.classList.contains('open'))return;if(e.key==='Escape'){lb.classList.remove('open');document.body.style.overflow=''}if(e.key==='ArrowLeft')lbShow(gi-1);if(e.key==='ArrowRight')lbShow(gi+1)});
  var tx=0;lb.addEventListener('touchstart',function(e){tx=e.touches[0].clientX},{passive:true});lb.addEventListener('touchend',function(e){var d=e.changedTouches[0].clientX-tx;if(Math.abs(d)>60)lbShow(gi+(d<0?1:-1))})}
 lbShow(gi);lb.classList.add('open');document.body.style.overflow='hidden'}
if(zw&&mi){zw.addEventListener('mousemove',function(e){if(!window.matchMedia('(hover:hover)').matches)return;var r=zw.getBoundingClientRect();mi.style.transformOrigin=((e.clientX-r.left)/r.width*100)+'% '+((e.clientY-r.top)/r.height*100)+'%';mi.style.transform='scale(2.2)'});zw.addEventListener('mouseleave',function(){mi.style.transform=''});zw.onclick=openLb;$('#zoomBtn').onclick=openLb}
$$('.thumbs img').forEach(function(t){t.onclick=function(){if(mi)mi.src=t.dataset.full;gi=+t.dataset.i||0;$$('.thumbs img').forEach(function(x){x.classList.remove('on')});t.classList.add('on')}});
/* cart page */
function cartSet(line,qty){api('set',{key:line.dataset.key,qty:qty}).then(function(r){setCount(r.count);if(qty<1){line.remove();if(!$$('.cline').length)location.reload()}else{$('.lt',line).textContent=r.lines[line.dataset.key]}$('#sSub').textContent=r.sub;if($('#sDisc'))$('#sDisc').textContent='-'+r.disc;$('#sTot').textContent=r.tot;loadCart(false)})}
$$('.cline').forEach(function(l){var inp=$('[data-cqin]',l);$$('[data-cq]',l).forEach(function(b){b.onclick=function(){inp.value=Math.min(+inp.max||99,Math.max(1,(+inp.value||1)+ +b.dataset.cq));cartSet(l,+inp.value)}});inp.onchange=function(){cartSet(l,Math.max(1,+inp.value||1))};$('[data-crm]',l).onclick=function(){cartSet(l,0)}});
var cb=$('#cpnBtn');if(cb)cb.onclick=function(){api('coupon',{code:$('#cpn').value}).then(function(r){var m=$('#cpnMsg');m.textContent=r.msg;m.style.color=r.ok?'#2b9d5c':'#d33';if(r.ok){$('#sDisc').textContent='-'+r.disc;$('#sTot').textContent=r.tot}})};
/* checkout: delivery area is chosen by the customer; contact details are saved as they type (abandoned-checkout follow-up) */
var co=$('#coForm');if(co){var sum=$('#coSum'),curS=window.CURS||'';
 var money=function(n){return curS+Math.round(n).toLocaleString('en-US')};
 var upd=function(){var zr=$('input[name=zone]:checked',co),z=zr?zr.value:'',sub=+sum.dataset.sub,dis=+sum.dataset.disc,fr=+sum.dataset.free,after=sub-dis,ship=0;if(z){ship=(fr>0&&after>=fr)?0:(z==='inside'?+sum.dataset.in:+sum.dataset.out);$('#coShip').textContent=ship?money(ship):T.freeW;$('#coTot').textContent=money(after+ship)}else{$('#coShip').textContent='—';$('#coTot').textContent=money(after)+' + '+T.shipW}};
 $$('input[name=zone]',co).forEach(function(r){r.onchange=upd});upd();
 var tm,send=function(){var f=new FormData();f.append('a','cv');f.append('_csrf',csrf);['name','phone','email','address'].forEach(function(k){if(co.elements[k])f.append(k,co.elements[k].value)});if(navigator.sendBeacon)navigator.sendBeacon(B+'/api.php',f);else fetch(B+'/api.php',{method:'POST',body:f,keepalive:true})};
 $$('input,textarea',co).forEach(function(i){i.addEventListener('input',function(){clearTimeout(tm);tm=setTimeout(send,900)})});document.addEventListener('visibilitychange',function(){if(document.visibilityState==='hidden')send()})}
loadCart(false);
})();
