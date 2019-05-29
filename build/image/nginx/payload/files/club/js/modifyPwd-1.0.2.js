!function (a, b) {
    "function" == typeof define && (define.amd || define.cmd) ? define(function () {
        return b(a)
    }) : b(a, !0)
}(this, function (a, b) {
    function c(b, c, d) {
        a.WeixinJSBridge ? WeixinJSBridge.invoke(b, e(c), function (a) {
            g(b, a, d)
        }) : j(b, d)
    }

    function d(b, c, d) {
        a.WeixinJSBridge ? WeixinJSBridge.on(b, function (a) {
            d && d.trigger && d.trigger(a), g(b, a, c)
        }) : d ? j(b, d) : j(b, c)
    }

    function e(a) {
        return a = a || {}, a.appId = E.appId, a.verifyAppId = E.appId, a.verifySignType = "sha1", a.verifyTimestamp = E.timestamp + "", a.verifyNonceStr = E.nonceStr, a.verifySignature = E.signature, a
    }

    function f(a) {
        return {
            timeStamp: a.timestamp + "",
            nonceStr: a.nonceStr,
            "package": a.package,
            paySign: a.paySign,
            signType: a.signType || "SHA1"
        }
    }

    function g(a, b, c) {
        var d, e, f;
        switch (delete b.err_code, delete b.err_desc, delete b.err_detail, d = b.errMsg, d || (d = b.err_msg, delete b.err_msg, d = h(a, d), b.errMsg = d), c = c || {}, c._complete && (c._complete(b), delete c._complete), d = b.errMsg || "", E.debug && !c.isInnerInvoke && alert(JSON.stringify(b)), e = d.indexOf(":"), f = d.substring(e + 1)) {
            case"ok":
                c.success && c.success(b);
                break;
            case"cancel":
                c.cancel && c.cancel(b);
                break;
            default:
                c.fail && c.fail(b)
        }
        c.complete && c.complete(b)
    }

    function h(a, b) {
        var e, f, c = a, d = p[c];
        return d && (c = d), e = "ok", b && (f = b.indexOf(":"), e = b.substring(f + 1), "confirm" == e && (e = "ok"), "failed" == e && (e = "fail"), -1 != e.indexOf("failed_") && (e = e.substring(7)), -1 != e.indexOf("fail_") && (e = e.substring(5)), e = e.replace(/_/g, " "), e = e.toLowerCase(), ("access denied" == e || "no permission to execute" == e) && (e = "permission denied"), "config" == c && "function not exist" == e && (e = "ok"), "" == e && (e = "fail")), b = c + ":" + e
    }

    function i(a) {
        var b, c, d, e;
        if (a) {
            for (b = 0, c = a.length; c > b; ++b) d = a[b], e = o[d], e && (a[b] = e);
            return a
        }
    }

    function j(a, b) {
        if (!(!E.debug || b && b.isInnerInvoke)) {
            var c = p[a];
            c && (a = c), b && b._complete && delete b._complete, console.log('"' + a + '",', b || "")
        }
    }

    function k() {
        0 != D.preVerifyState && (u || v || E.debug || "6.0.2" > z || D.systemType < 0 || A || (A = !0, D.appId = E.appId, D.initTime = C.initEndTime - C.initStartTime, D.preVerifyTime = C.preVerifyEndTime - C.preVerifyStartTime, H.getNetworkType({
            isInnerInvoke: !0,
            success: function (a) {
                var b, c;
                D.networkType = a.networkType, b = "http://open.weixin.qq.com/sdk/report?v=" + D.version + "&o=" + D.preVerifyState + "&s=" + D.systemType + "&c=" + D.clientVersion + "&a=" + D.appId + "&n=" + D.networkType + "&i=" + D.initTime + "&p=" + D.preVerifyTime + "&u=" + D.url, c = new Image, c.src = b
            }
        })))
    }

    function l() {
        return (new Date).getTime()
    }

    function m(b) {
        w && (a.WeixinJSBridge ? b() : q.addEventListener && q.addEventListener("WeixinJSBridgeReady", b, !1))
    }

    function n() {
        H.invoke || (H.invoke = function (b, c, d) {
            a.WeixinJSBridge && WeixinJSBridge.invoke(b, e(c), d)
        }, H.on = function (b, c) {
            a.WeixinJSBridge && WeixinJSBridge.on(b, c)
        })
    }

    var o, p, q, r, s, t, u, v, w, x, y, z, A, B, C, D, E, F, G, H;
    if (!a.jWeixin) return o = {
        config: "preVerifyJSAPI",
        onMenuShareTimeline: "menu:share:timeline",
        onMenuShareAppMessage: "menu:share:appmessage",
        onMenuShareQQ: "menu:share:qq",
        onMenuShareWeibo: "menu:share:weiboApp",
        onMenuShareQZone: "menu:share:QZone",
        previewImage: "imagePreview",
        getLocation: "geoLocation",
        openProductSpecificView: "openProductViewWithPid",
        addCard: "batchAddCard",
        openCard: "batchViewCard",
        chooseWXPay: "getBrandWCPayRequest"
    }, p = function () {
        var b, a = {};
        for (b in o) a[o[b]] = b;
        return a
    }(), q = a.document, r = q.title, s = navigator.userAgent.toLowerCase(), t = navigator.platform.toLowerCase(), u = !(!t.match("mac") && !t.match("win")), v = -1 != s.indexOf("wxdebugger"), w = -1 != s.indexOf("micromessenger"), x = -1 != s.indexOf("android"), y = -1 != s.indexOf("iphone") || -1 != s.indexOf("ipad"), z = function () {
        var a = s.match(/micromessenger\/(\d+\.\d+\.\d+)/) || s.match(/micromessenger\/(\d+\.\d+)/);
        return a ? a[1] : ""
    }(), A = !1, B = !1, C = {
        initStartTime: l(),
        initEndTime: 0,
        preVerifyStartTime: 0,
        preVerifyEndTime: 0
    }, D = {
        version: 1,
        appId: "",
        initTime: 0,
        preVerifyTime: 0,
        networkType: "",
        preVerifyState: 1,
        systemType: y ? 1 : x ? 2 : -1,
        clientVersion: z,
        url: encodeURIComponent(location.href)
    }, E = {}, F = {_completes: []}, G = {state: 0, data: {}}, m(function () {
        C.initEndTime = l()
    }), H = {
        config: function (a) {
            E = a, j("config", a);
            var b = E.check === !1 ? !1 : !0;
            m(function () {
                var a, d, e;
                if (b) c(o.config, {verifyJsApiList: i(E.jsApiList)}, function () {
                    F._complete = function (a) {
                        C.preVerifyEndTime = l(), G.state = 1, G.data = a
                    }, F.success = function () {
                        D.preVerifyState = 0
                    }, F.fail = function (a) {
                        F._fail ? F._fail(a) : G.state = -1
                    };
                    var a = F._completes;
                    return a.push(function () {
                        k()
                    }), F.complete = function () {
                        for (var c = 0, d = a.length; d > c; ++c) a[c]();
                        F._completes = []
                    }, F
                }()), C.preVerifyStartTime = l(); else {
                    for (G.state = 1, a = F._completes, d = 0, e = a.length; e > d; ++d) a[d]();
                    F._completes = []
                }
            }), E.beta && n()
        }, ready: function (a) {
            0 != G.state ? a() : (F._completes.push(a), !w && E.debug && a())
        }, error: function (a) {
            "6.0.2" > z || B || (B = !0, -1 == G.state ? a(G.data) : F._fail = a)
        }, checkJsApi: function (a) {
            var b = function (a) {
                var c, d, b = a.checkResult;
                for (c in b) d = p[c], d && (b[d] = b[c], delete b[c]);
                return a
            };
            c("checkJsApi", {jsApiList: i(a.jsApiList)}, function () {
                return a._complete = function (a) {
                    if (x) {
                        var c = a.checkResult;
                        c && (a.checkResult = JSON.parse(c))
                    }
                    a = b(a)
                }, a
            }())
        }, onMenuShareTimeline: function (a) {
            d(o.onMenuShareTimeline, {
                complete: function () {
                    c("shareTimeline", {
                        title: a.title || r,
                        desc: a.title || r,
                        img_url: a.imgUrl || "",
                        link: a.link || location.href,
                        type: a.type || "link",
                        data_url: a.dataUrl || ""
                    }, a)
                }
            }, a)
        }, onMenuShareAppMessage: function (a) {
            d(o.onMenuShareAppMessage, {
                complete: function () {
                    c("sendAppMessage", {
                        title: a.title || r,
                        desc: a.desc || "",
                        link: a.link || location.href,
                        img_url: a.imgUrl || "",
                        type: a.type || "link",
                        data_url: a.dataUrl || ""
                    }, a)
                }
            }, a)
        }, onMenuShareQQ: function (a) {
            d(o.onMenuShareQQ, {
                complete: function () {
                    c("shareQQ", {
                        title: a.title || r,
                        desc: a.desc || "",
                        img_url: a.imgUrl || "",
                        link: a.link || location.href
                    }, a)
                }
            }, a)
        }, onMenuShareWeibo: function (a) {
            d(o.onMenuShareWeibo, {
                complete: function () {
                    c("shareWeiboApp", {
                        title: a.title || r,
                        desc: a.desc || "",
                        img_url: a.imgUrl || "",
                        link: a.link || location.href
                    }, a)
                }
            }, a)
        }, onMenuShareQZone: function (a) {
            d(o.onMenuShareQZone, {
                complete: function () {
                    c("shareQZone", {
                        title: a.title || r,
                        desc: a.desc || "",
                        img_url: a.imgUrl || "",
                        link: a.link || location.href
                    }, a)
                }
            }, a)
        }, startRecord: function (a) {
            c("startRecord", {}, a)
        }, stopRecord: function (a) {
            c("stopRecord", {}, a)
        }, onVoiceRecordEnd: function (a) {
            d("onVoiceRecordEnd", a)
        }, playVoice: function (a) {
            c("playVoice", {localId: a.localId}, a)
        }, pauseVoice: function (a) {
            c("pauseVoice", {localId: a.localId}, a)
        }, stopVoice: function (a) {
            c("stopVoice", {localId: a.localId}, a)
        }, onVoicePlayEnd: function (a) {
            d("onVoicePlayEnd", a)
        }, uploadVoice: function (a) {
            c("uploadVoice", {localId: a.localId, isShowProgressTips: 0 == a.isShowProgressTips ? 0 : 1}, a)
        }, downloadVoice: function (a) {
            c("downloadVoice", {serverId: a.serverId, isShowProgressTips: 0 == a.isShowProgressTips ? 0 : 1}, a)
        }, translateVoice: function (a) {
            c("translateVoice", {localId: a.localId, isShowProgressTips: 0 == a.isShowProgressTips ? 0 : 1}, a)
        }, chooseImage: function (a) {
            c("chooseImage", {
                scene: "1|2",
                count: a.count || 9,
                sizeType: a.sizeType || ["original", "compressed"],
                sourceType: a.sourceType || ["album", "camera"]
            }, function () {
                return a._complete = function (a) {
                    if (x) {
                        var b = a.localIds;
                        b && (a.localIds = JSON.parse(b))
                    }
                }, a
            }())
        }, previewImage: function (a) {
            c(o.previewImage, {current: a.current, urls: a.urls}, a)
        }, uploadImage: function (a) {
            c("uploadImage", {localId: a.localId, isShowProgressTips: 0 == a.isShowProgressTips ? 0 : 1}, a)
        }, downloadImage: function (a) {
            c("downloadImage", {serverId: a.serverId, isShowProgressTips: 0 == a.isShowProgressTips ? 0 : 1}, a)
        }, getNetworkType: function (a) {
            var b = function (a) {
                var c, d, e, b = a.errMsg;
                if (a.errMsg = "getNetworkType:ok", c = a.subtype, delete a.subtype, c) a.networkType = c; else switch (d = b.indexOf(":"), e = b.substring(d + 1)) {
                    case"wifi":
                    case"edge":
                    case"wwan":
                        a.networkType = e;
                        break;
                    default:
                        a.errMsg = "getNetworkType:fail"
                }
                return a
            };
            c("getNetworkType", {}, function () {
                return a._complete = function (a) {
                    a = b(a)
                }, a
            }())
        }, openLocation: function (a) {
            c("openLocation", {
                latitude: a.latitude,
                longitude: a.longitude,
                name: a.name || "",
                address: a.address || "",
                scale: a.scale || 28,
                infoUrl: a.infoUrl || ""
            }, a)
        }, getLocation: function (a) {
            a = a || {}, c(o.getLocation, {type: a.type || "wgs84"}, function () {
                return a._complete = function (a) {
                    delete a.type
                }, a
            }())
        }, hideOptionMenu: function (a) {
            c("hideOptionMenu", {}, a)
        }, showOptionMenu: function (a) {
            c("showOptionMenu", {}, a)
        }, closeWindow: function (a) {
            a = a || {}, c("closeWindow", {}, a)
        }, hideMenuItems: function (a) {
            c("hideMenuItems", {menuList: a.menuList}, a)
        }, showMenuItems: function (a) {
            c("showMenuItems", {menuList: a.menuList}, a)
        }, hideAllNonBaseMenuItem: function (a) {
            c("hideAllNonBaseMenuItem", {}, a)
        }, showAllNonBaseMenuItem: function (a) {
            c("showAllNonBaseMenuItem", {}, a)
        }, scanQRCode: function (a) {
            a = a || {}, c("scanQRCode", {
                needResult: a.needResult || 0,
                scanType: a.scanType || ["qrCode", "barCode"]
            }, function () {
                return a._complete = function (a) {
                    var b, c;
                    y && (b = a.resultStr, b && (c = JSON.parse(b), a.resultStr = c && c.scan_code && c.scan_code.scan_result))
                }, a
            }())
        }, openProductSpecificView: function (a) {
            c(o.openProductSpecificView, {pid: a.productId, view_type: a.viewType || 0, ext_info: a.extInfo}, a)
        }, addCard: function (a) {
            var e, f, g, h, b = a.cardList, d = [];
            for (e = 0, f = b.length; f > e; ++e) g = b[e], h = {card_id: g.cardId, card_ext: g.cardExt}, d.push(h);
            c(o.addCard, {card_list: d}, function () {
                return a._complete = function (a) {
                    var c, d, e, b = a.card_list;
                    if (b) {
                        for (b = JSON.parse(b), c = 0, d = b.length; d > c; ++c) e = b[c], e.cardId = e.card_id, e.cardExt = e.card_ext, e.isSuccess = e.is_succ ? !0 : !1, delete e.card_id, delete e.card_ext, delete e.is_succ;
                        a.cardList = b, delete a.card_list
                    }
                }, a
            }())
        }, chooseCard: function (a) {
            c("chooseCard", {
                app_id: E.appId,
                location_id: a.shopId || "",
                sign_type: a.signType || "SHA1",
                card_id: a.cardId || "",
                card_type: a.cardType || "",
                card_sign: a.cardSign,
                time_stamp: a.timestamp + "",
                nonce_str: a.nonceStr
            }, function () {
                return a._complete = function (a) {
                    a.cardList = a.choose_card_info, delete a.choose_card_info
                }, a
            }())
        }, openCard: function (a) {
            var e, f, g, h, b = a.cardList, d = [];
            for (e = 0, f = b.length; f > e; ++e) g = b[e], h = {card_id: g.cardId, code: g.code}, d.push(h);
            c(o.openCard, {card_list: d}, a)
        }, chooseWXPay: function (a) {
            c(o.chooseWXPay, f(a), a)
        }
    }, b && (a.wx = a.jWeixin = H), H
});
/*! jQuery v1.9.1 | (c) 2005, 2012 jQuery Foundation, Inc. | jquery.org/license
//@ sourceMappingURL=jquery.min.map
*/
(function (e, t) {
    var n, r, i = typeof t, o = e.document, a = e.location, s = e.jQuery, u = e.$, l = {}, c = [], p = "1.9.1",
        f = c.concat, d = c.push, h = c.slice, g = c.indexOf, m = l.toString, y = l.hasOwnProperty, v = p.trim,
        b = function (e, t) {
            return new b.fn.init(e, t, r)
        }, x = /[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source, w = /\S+/g, T = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,
        N = /^(?:(<[\w\W]+>)[^>]*|#([\w-]*))$/, C = /^<(\w+)\s*\/?>(?:<\/\1>|)$/, k = /^[\],:{}\s]*$/,
        E = /(?:^|:|,)(?:\s*\[)+/g, S = /\\(?:["\\\/bfnrt]|u[\da-fA-F]{4})/g,
        A = /"[^"\\\r\n]*"|true|false|null|-?(?:\d+\.|)\d+(?:[eE][+-]?\d+|)/g, j = /^-ms-/, D = /-([\da-z])/gi,
        L = function (e, t) {
            return t.toUpperCase()
        }, H = function (e) {
            (o.addEventListener || "load" === e.type || "complete" === o.readyState) && (q(), b.ready())
        }, q = function () {
            o.addEventListener ? (o.removeEventListener("DOMContentLoaded", H, !1), e.removeEventListener("load", H, !1)) : (o.detachEvent("onreadystatechange", H), e.detachEvent("onload", H))
        };
    b.fn = b.prototype = {
        jquery: p, constructor: b, init: function (e, n, r) {
            var i, a;
            if (!e) return this;
            if ("string" == typeof e) {
                if (i = "<" === e.charAt(0) && ">" === e.charAt(e.length - 1) && e.length >= 3 ? [null, e, null] : N.exec(e), !i || !i[1] && n) return !n || n.jquery ? (n || r).find(e) : this.constructor(n).find(e);
                if (i[1]) {
                    if (n = n instanceof b ? n[0] : n, b.merge(this, b.parseHTML(i[1], n && n.nodeType ? n.ownerDocument || n : o, !0)), C.test(i[1]) && b.isPlainObject(n)) for (i in n) b.isFunction(this[i]) ? this[i](n[i]) : this.attr(i, n[i]);
                    return this
                }
                if (a = o.getElementById(i[2]), a && a.parentNode) {
                    if (a.id !== i[2]) return r.find(e);
                    this.length = 1, this[0] = a
                }
                return this.context = o, this.selector = e, this
            }
            return e.nodeType ? (this.context = this[0] = e, this.length = 1, this) : b.isFunction(e) ? r.ready(e) : (e.selector !== t && (this.selector = e.selector, this.context = e.context), b.makeArray(e, this))
        }, selector: "", length: 0, size: function () {
            return this.length
        }, toArray: function () {
            return h.call(this)
        }, get: function (e) {
            return null == e ? this.toArray() : 0 > e ? this[this.length + e] : this[e]
        }, pushStack: function (e) {
            var t = b.merge(this.constructor(), e);
            return t.prevObject = this, t.context = this.context, t
        }, each: function (e, t) {
            return b.each(this, e, t)
        }, ready: function (e) {
            return b.ready.promise().done(e), this
        }, slice: function () {
            return this.pushStack(h.apply(this, arguments))
        }, first: function () {
            return this.eq(0)
        }, last: function () {
            return this.eq(-1)
        }, eq: function (e) {
            var t = this.length, n = +e + (0 > e ? t : 0);
            return this.pushStack(n >= 0 && t > n ? [this[n]] : [])
        }, map: function (e) {
            return this.pushStack(b.map(this, function (t, n) {
                return e.call(t, n, t)
            }))
        }, end: function () {
            return this.prevObject || this.constructor(null)
        }, push: d, sort: [].sort, splice: [].splice
    }, b.fn.init.prototype = b.fn, b.extend = b.fn.extend = function () {
        var e, n, r, i, o, a, s = arguments[0] || {}, u = 1, l = arguments.length, c = !1;
        for ("boolean" == typeof s && (c = s, s = arguments[1] || {}, u = 2), "object" == typeof s || b.isFunction(s) || (s = {}), l === u && (s = this, --u); l > u; u++) if (null != (o = arguments[u])) for (i in o) e = s[i], r = o[i], s !== r && (c && r && (b.isPlainObject(r) || (n = b.isArray(r))) ? (n ? (n = !1, a = e && b.isArray(e) ? e : []) : a = e && b.isPlainObject(e) ? e : {}, s[i] = b.extend(c, a, r)) : r !== t && (s[i] = r));
        return s
    }, b.extend({
        noConflict: function (t) {
            return e.$ === b && (e.$ = u), t && e.jQuery === b && (e.jQuery = s), b
        }, isReady: !1, readyWait: 1, holdReady: function (e) {
            e ? b.readyWait++ : b.ready(!0)
        }, ready: function (e) {
            if (e === !0 ? !--b.readyWait : !b.isReady) {
                if (!o.body) return setTimeout(b.ready);
                b.isReady = !0, e !== !0 && --b.readyWait > 0 || (n.resolveWith(o, [b]), b.fn.trigger && b(o).trigger("ready").off("ready"))
            }
        }, isFunction: function (e) {
            return "function" === b.type(e)
        }, isArray: Array.isArray || function (e) {
            return "array" === b.type(e)
        }, isWindow: function (e) {
            return null != e && e == e.window
        }, isNumeric: function (e) {
            return !isNaN(parseFloat(e)) && isFinite(e)
        }, type: function (e) {
            return null == e ? e + "" : "object" == typeof e || "function" == typeof e ? l[m.call(e)] || "object" : typeof e
        }, isPlainObject: function (e) {
            if (!e || "object" !== b.type(e) || e.nodeType || b.isWindow(e)) return !1;
            try {
                if (e.constructor && !y.call(e, "constructor") && !y.call(e.constructor.prototype, "isPrototypeOf")) return !1
            } catch (n) {
                return !1
            }
            var r;
            for (r in e) ;
            return r === t || y.call(e, r)
        }, isEmptyObject: function (e) {
            var t;
            for (t in e) return !1;
            return !0
        }, error: function (e) {
            throw Error(e)
        }, parseHTML: function (e, t, n) {
            if (!e || "string" != typeof e) return null;
            "boolean" == typeof t && (n = t, t = !1), t = t || o;
            var r = C.exec(e), i = !n && [];
            return r ? [t.createElement(r[1])] : (r = b.buildFragment([e], t, i), i && b(i).remove(), b.merge([], r.childNodes))
        }, parseJSON: function (n) {
            return e.JSON && e.JSON.parse ? e.JSON.parse(n) : null === n ? n : "string" == typeof n && (n = b.trim(n), n && k.test(n.replace(S, "@").replace(A, "]").replace(E, ""))) ? Function("return " + n)() : (b.error("Invalid JSON: " + n), t)
        }, parseXML: function (n) {
            var r, i;
            if (!n || "string" != typeof n) return null;
            try {
                e.DOMParser ? (i = new DOMParser, r = i.parseFromString(n, "text/xml")) : (r = new ActiveXObject("Microsoft.XMLDOM"), r.async = "false", r.loadXML(n))
            } catch (o) {
                r = t
            }
            return r && r.documentElement && !r.getElementsByTagName("parsererror").length || b.error("Invalid XML: " + n), r
        }, noop: function () {
        }, globalEval: function (t) {
            t && b.trim(t) && (e.execScript || function (t) {
                e.eval.call(e, t)
            })(t)
        }, camelCase: function (e) {
            return e.replace(j, "ms-").replace(D, L)
        }, nodeName: function (e, t) {
            return e.nodeName && e.nodeName.toLowerCase() === t.toLowerCase()
        }, each: function (e, t, n) {
            var r, i = 0, o = e.length, a = M(e);
            if (n) {
                if (a) {
                    for (; o > i; i++) if (r = t.apply(e[i], n), r === !1) break
                } else for (i in e) if (r = t.apply(e[i], n), r === !1) break
            } else if (a) {
                for (; o > i; i++) if (r = t.call(e[i], i, e[i]), r === !1) break
            } else for (i in e) if (r = t.call(e[i], i, e[i]), r === !1) break;
            return e
        }, trim: v && !v.call("\ufeff\u00a0") ? function (e) {
            return null == e ? "" : v.call(e)
        } : function (e) {
            return null == e ? "" : (e + "").replace(T, "")
        }, makeArray: function (e, t) {
            var n = t || [];
            return null != e && (M(Object(e)) ? b.merge(n, "string" == typeof e ? [e] : e) : d.call(n, e)), n
        }, inArray: function (e, t, n) {
            var r;
            if (t) {
                if (g) return g.call(t, e, n);
                for (r = t.length, n = n ? 0 > n ? Math.max(0, r + n) : n : 0; r > n; n++) if (n in t && t[n] === e) return n
            }
            return -1
        }, merge: function (e, n) {
            var r = n.length, i = e.length, o = 0;
            if ("number" == typeof r) for (; r > o; o++) e[i++] = n[o]; else while (n[o] !== t) e[i++] = n[o++];
            return e.length = i, e
        }, grep: function (e, t, n) {
            var r, i = [], o = 0, a = e.length;
            for (n = !!n; a > o; o++) r = !!t(e[o], o), n !== r && i.push(e[o]);
            return i
        }, map: function (e, t, n) {
            var r, i = 0, o = e.length, a = M(e), s = [];
            if (a) for (; o > i; i++) r = t(e[i], i, n), null != r && (s[s.length] = r); else for (i in e) r = t(e[i], i, n), null != r && (s[s.length] = r);
            return f.apply([], s)
        }, guid: 1, proxy: function (e, n) {
            var r, i, o;
            return "string" == typeof n && (o = e[n], n = e, e = o), b.isFunction(e) ? (r = h.call(arguments, 2), i = function () {
                return e.apply(n || this, r.concat(h.call(arguments)))
            }, i.guid = e.guid = e.guid || b.guid++, i) : t
        }, access: function (e, n, r, i, o, a, s) {
            var u = 0, l = e.length, c = null == r;
            if ("object" === b.type(r)) {
                o = !0;
                for (u in r) b.access(e, n, u, r[u], !0, a, s)
            } else if (i !== t && (o = !0, b.isFunction(i) || (s = !0), c && (s ? (n.call(e, i), n = null) : (c = n, n = function (e, t, n) {
                    return c.call(b(e), n)
                })), n)) for (; l > u; u++) n(e[u], r, s ? i : i.call(e[u], u, n(e[u], r)));
            return o ? e : c ? n.call(e) : l ? n(e[0], r) : a
        }, now: function () {
            return (new Date).getTime()
        }
    }), b.ready.promise = function (t) {
        if (!n) if (n = b.Deferred(), "complete" === o.readyState) setTimeout(b.ready); else if (o.addEventListener) o.addEventListener("DOMContentLoaded", H, !1), e.addEventListener("load", H, !1); else {
            o.attachEvent("onreadystatechange", H), e.attachEvent("onload", H);
            var r = !1;
            try {
                r = null == e.frameElement && o.documentElement
            } catch (i) {
            }
            r && r.doScroll && function a() {
                if (!b.isReady) {
                    try {
                        r.doScroll("left")
                    } catch (e) {
                        return setTimeout(a, 50)
                    }
                    q(), b.ready()
                }
            }()
        }
        return n.promise(t)
    }, b.each("Boolean Number String Function Array Date RegExp Object Error".split(" "), function (e, t) {
        l["[object " + t + "]"] = t.toLowerCase()
    });

    function M(e) {
        var t = e.length, n = b.type(e);
        return b.isWindow(e) ? !1 : 1 === e.nodeType && t ? !0 : "array" === n || "function" !== n && (0 === t || "number" == typeof t && t > 0 && t - 1 in e)
    }

    r = b(o);
    var _ = {};

    function F(e) {
        var t = _[e] = {};
        return b.each(e.match(w) || [], function (e, n) {
            t[n] = !0
        }), t
    }

    b.Callbacks = function (e) {
        e = "string" == typeof e ? _[e] || F(e) : b.extend({}, e);
        var n, r, i, o, a, s, u = [], l = !e.once && [], c = function (t) {
            for (r = e.memory && t, i = !0, a = s || 0, s = 0, o = u.length, n = !0; u && o > a; a++) if (u[a].apply(t[0], t[1]) === !1 && e.stopOnFalse) {
                r = !1;
                break
            }
            n = !1, u && (l ? l.length && c(l.shift()) : r ? u = [] : p.disable())
        }, p = {
            add: function () {
                if (u) {
                    var t = u.length;
                    (function i(t) {
                        b.each(t, function (t, n) {
                            var r = b.type(n);
                            "function" === r ? e.unique && p.has(n) || u.push(n) : n && n.length && "string" !== r && i(n)
                        })
                    })(arguments), n ? o = u.length : r && (s = t, c(r))
                }
                return this
            }, remove: function () {
                return u && b.each(arguments, function (e, t) {
                    var r;
                    while ((r = b.inArray(t, u, r)) > -1) u.splice(r, 1), n && (o >= r && o--, a >= r && a--)
                }), this
            }, has: function (e) {
                return e ? b.inArray(e, u) > -1 : !(!u || !u.length)
            }, empty: function () {
                return u = [], this
            }, disable: function () {
                return u = l = r = t, this
            }, disabled: function () {
                return !u
            }, lock: function () {
                return l = t, r || p.disable(), this
            }, locked: function () {
                return !l
            }, fireWith: function (e, t) {
                return t = t || [], t = [e, t.slice ? t.slice() : t], !u || i && !l || (n ? l.push(t) : c(t)), this
            }, fire: function () {
                return p.fireWith(this, arguments), this
            }, fired: function () {
                return !!i
            }
        };
        return p
    }, b.extend({
        Deferred: function (e) {
            var t = [["resolve", "done", b.Callbacks("once memory"), "resolved"], ["reject", "fail", b.Callbacks("once memory"), "rejected"], ["notify", "progress", b.Callbacks("memory")]],
                n = "pending", r = {
                    state: function () {
                        return n
                    }, always: function () {
                        return i.done(arguments).fail(arguments), this
                    }, then: function () {
                        var e = arguments;
                        return b.Deferred(function (n) {
                            b.each(t, function (t, o) {
                                var a = o[0], s = b.isFunction(e[t]) && e[t];
                                i[o[1]](function () {
                                    var e = s && s.apply(this, arguments);
                                    e && b.isFunction(e.promise) ? e.promise().done(n.resolve).fail(n.reject).progress(n.notify) : n[a + "With"](this === r ? n.promise() : this, s ? [e] : arguments)
                                })
                            }), e = null
                        }).promise()
                    }, promise: function (e) {
                        return null != e ? b.extend(e, r) : r
                    }
                }, i = {};
            return r.pipe = r.then, b.each(t, function (e, o) {
                var a = o[2], s = o[3];
                r[o[1]] = a.add, s && a.add(function () {
                    n = s
                }, t[1 ^ e][2].disable, t[2][2].lock), i[o[0]] = function () {
                    return i[o[0] + "With"](this === i ? r : this, arguments), this
                }, i[o[0] + "With"] = a.fireWith
            }), r.promise(i), e && e.call(i, i), i
        }, when: function (e) {
            var t = 0, n = h.call(arguments), r = n.length, i = 1 !== r || e && b.isFunction(e.promise) ? r : 0,
                o = 1 === i ? e : b.Deferred(), a = function (e, t, n) {
                    return function (r) {
                        t[e] = this, n[e] = arguments.length > 1 ? h.call(arguments) : r, n === s ? o.notifyWith(t, n) : --i || o.resolveWith(t, n)
                    }
                }, s, u, l;
            if (r > 1) for (s = Array(r), u = Array(r), l = Array(r); r > t; t++) n[t] && b.isFunction(n[t].promise) ? n[t].promise().done(a(t, l, n)).fail(o.reject).progress(a(t, u, s)) : --i;
            return i || o.resolveWith(l, n), o.promise()
        }
    }), b.support = function () {
        var t, n, r, a, s, u, l, c, p, f, d = o.createElement("div");
        if (d.setAttribute("className", "t"), d.innerHTML = "  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>", n = d.getElementsByTagName("*"), r = d.getElementsByTagName("a")[0], !n || !r || !n.length) return {};
        s = o.createElement("select"), l = s.appendChild(o.createElement("option")), a = d.getElementsByTagName("input")[0], r.style.cssText = "top:1px;float:left;opacity:.5", t = {
            getSetAttribute: "t" !== d.className,
            leadingWhitespace: 3 === d.firstChild.nodeType,
            tbody: !d.getElementsByTagName("tbody").length,
            htmlSerialize: !!d.getElementsByTagName("link").length,
            style: /top/.test(r.getAttribute("style")),
            hrefNormalized: "/a" === r.getAttribute("href"),
            opacity: /^0.5/.test(r.style.opacity),
            cssFloat: !!r.style.cssFloat,
            checkOn: !!a.value,
            optSelected: l.selected,
            enctype: !!o.createElement("form").enctype,
            html5Clone: "<:nav></:nav>" !== o.createElement("nav").cloneNode(!0).outerHTML,
            boxModel: "CSS1Compat" === o.compatMode,
            deleteExpando: !0,
            noCloneEvent: !0,
            inlineBlockNeedsLayout: !1,
            shrinkWrapBlocks: !1,
            reliableMarginRight: !0,
            boxSizingReliable: !0,
            pixelPosition: !1
        }, a.checked = !0, t.noCloneChecked = a.cloneNode(!0).checked, s.disabled = !0, t.optDisabled = !l.disabled;
        try {
            delete d.test
        } catch (h) {
            t.deleteExpando = !1
        }
        a = o.createElement("input"), a.setAttribute("value", ""), t.input = "" === a.getAttribute("value"), a.value = "t", a.setAttribute("type", "radio"), t.radioValue = "t" === a.value, a.setAttribute("checked", "t"), a.setAttribute("name", "t"), u = o.createDocumentFragment(), u.appendChild(a), t.appendChecked = a.checked, t.checkClone = u.cloneNode(!0).cloneNode(!0).lastChild.checked, d.attachEvent && (d.attachEvent("onclick", function () {
            t.noCloneEvent = !1
        }), d.cloneNode(!0).click());
        for (f in{
            submit: !0,
            change: !0,
            focusin: !0
        }) d.setAttribute(c = "on" + f, "t"), t[f + "Bubbles"] = c in e || d.attributes[c].expando === !1;
        return d.style.backgroundClip = "content-box", d.cloneNode(!0).style.backgroundClip = "", t.clearCloneStyle = "content-box" === d.style.backgroundClip, b(function () {
            var n, r, a,
                s = "padding:0;margin:0;border:0;display:block;box-sizing:content-box;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;",
                u = o.getElementsByTagName("body")[0];
            u && (n = o.createElement("div"), n.style.cssText = "border:0;width:0;height:0;position:absolute;top:0;left:-9999px;margin-top:1px", u.appendChild(n).appendChild(d), d.innerHTML = "<table><tr><td></td><td>t</td></tr></table>", a = d.getElementsByTagName("td"), a[0].style.cssText = "padding:0;margin:0;border:0;display:none", p = 0 === a[0].offsetHeight, a[0].style.display = "", a[1].style.display = "none", t.reliableHiddenOffsets = p && 0 === a[0].offsetHeight, d.innerHTML = "", d.style.cssText = "box-sizing:border-box;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;padding:1px;border:1px;display:block;width:4px;margin-top:1%;position:absolute;top:1%;", t.boxSizing = 4 === d.offsetWidth, t.doesNotIncludeMarginInBodyOffset = 1 !== u.offsetTop, e.getComputedStyle && (t.pixelPosition = "1%" !== (e.getComputedStyle(d, null) || {}).top, t.boxSizingReliable = "4px" === (e.getComputedStyle(d, null) || {width: "4px"}).width, r = d.appendChild(o.createElement("div")), r.style.cssText = d.style.cssText = s, r.style.marginRight = r.style.width = "0", d.style.width = "1px", t.reliableMarginRight = !parseFloat((e.getComputedStyle(r, null) || {}).marginRight)), typeof d.style.zoom !== i && (d.innerHTML = "", d.style.cssText = s + "width:1px;padding:1px;display:inline;zoom:1", t.inlineBlockNeedsLayout = 3 === d.offsetWidth, d.style.display = "block", d.innerHTML = "<div></div>", d.firstChild.style.width = "5px", t.shrinkWrapBlocks = 3 !== d.offsetWidth, t.inlineBlockNeedsLayout && (u.style.zoom = 1)), u.removeChild(n), n = d = a = r = null)
        }), n = s = u = l = r = a = null, t
    }();
    var O = /(?:\{[\s\S]*\}|\[[\s\S]*\])$/, B = /([A-Z])/g;

    function P(e, n, r, i) {
        if (b.acceptData(e)) {
            var o, a, s = b.expando, u = "string" == typeof n, l = e.nodeType, p = l ? b.cache : e,
                f = l ? e[s] : e[s] && s;
            if (f && p[f] && (i || p[f].data) || !u || r !== t) return f || (l ? e[s] = f = c.pop() || b.guid++ : f = s), p[f] || (p[f] = {}, l || (p[f].toJSON = b.noop)), ("object" == typeof n || "function" == typeof n) && (i ? p[f] = b.extend(p[f], n) : p[f].data = b.extend(p[f].data, n)), o = p[f], i || (o.data || (o.data = {}), o = o.data), r !== t && (o[b.camelCase(n)] = r), u ? (a = o[n], null == a && (a = o[b.camelCase(n)])) : a = o, a
        }
    }

    function R(e, t, n) {
        if (b.acceptData(e)) {
            var r, i, o, a = e.nodeType, s = a ? b.cache : e, u = a ? e[b.expando] : b.expando;
            if (s[u]) {
                if (t && (o = n ? s[u] : s[u].data)) {
                    b.isArray(t) ? t = t.concat(b.map(t, b.camelCase)) : t in o ? t = [t] : (t = b.camelCase(t), t = t in o ? [t] : t.split(" "));
                    for (r = 0, i = t.length; i > r; r++) delete o[t[r]];
                    if (!(n ? $ : b.isEmptyObject)(o)) return
                }
                (n || (delete s[u].data, $(s[u]))) && (a ? b.cleanData([e], !0) : b.support.deleteExpando || s != s.window ? delete s[u] : s[u] = null)
            }
        }
    }

    b.extend({
        cache: {},
        expando: "jQuery" + (p + Math.random()).replace(/\D/g, ""),
        noData: {embed: !0, object: "clsid:D27CDB6E-AE6D-11cf-96B8-444553540000", applet: !0},
        hasData: function (e) {
            return e = e.nodeType ? b.cache[e[b.expando]] : e[b.expando], !!e && !$(e)
        },
        data: function (e, t, n) {
            return P(e, t, n)
        },
        removeData: function (e, t) {
            return R(e, t)
        },
        _data: function (e, t, n) {
            return P(e, t, n, !0)
        },
        _removeData: function (e, t) {
            return R(e, t, !0)
        },
        acceptData: function (e) {
            if (e.nodeType && 1 !== e.nodeType && 9 !== e.nodeType) return !1;
            var t = e.nodeName && b.noData[e.nodeName.toLowerCase()];
            return !t || t !== !0 && e.getAttribute("classid") === t
        }
    }), b.fn.extend({
        data: function (e, n) {
            var r, i, o = this[0], a = 0, s = null;
            if (e === t) {
                if (this.length && (s = b.data(o), 1 === o.nodeType && !b._data(o, "parsedAttrs"))) {
                    for (r = o.attributes; r.length > a; a++) i = r[a].name, i.indexOf("data-") || (i = b.camelCase(i.slice(5)), W(o, i, s[i]));
                    b._data(o, "parsedAttrs", !0)
                }
                return s
            }
            return "object" == typeof e ? this.each(function () {
                b.data(this, e)
            }) : b.access(this, function (n) {
                return n === t ? o ? W(o, e, b.data(o, e)) : null : (this.each(function () {
                    b.data(this, e, n)
                }), t)
            }, null, n, arguments.length > 1, null, !0)
        }, removeData: function (e) {
            return this.each(function () {
                b.removeData(this, e)
            })
        }
    });

    function W(e, n, r) {
        if (r === t && 1 === e.nodeType) {
            var i = "data-" + n.replace(B, "-$1").toLowerCase();
            if (r = e.getAttribute(i), "string" == typeof r) {
                try {
                    r = "true" === r ? !0 : "false" === r ? !1 : "null" === r ? null : +r + "" === r ? +r : O.test(r) ? b.parseJSON(r) : r
                } catch (o) {
                }
                b.data(e, n, r)
            } else r = t
        }
        return r
    }

    function $(e) {
        var t;
        for (t in e) if (("data" !== t || !b.isEmptyObject(e[t])) && "toJSON" !== t) return !1;
        return !0
    }

    b.extend({
        queue: function (e, n, r) {
            var i;
            return e ? (n = (n || "fx") + "queue", i = b._data(e, n), r && (!i || b.isArray(r) ? i = b._data(e, n, b.makeArray(r)) : i.push(r)), i || []) : t
        }, dequeue: function (e, t) {
            t = t || "fx";
            var n = b.queue(e, t), r = n.length, i = n.shift(), o = b._queueHooks(e, t), a = function () {
                b.dequeue(e, t)
            };
            "inprogress" === i && (i = n.shift(), r--), o.cur = i, i && ("fx" === t && n.unshift("inprogress"), delete o.stop, i.call(e, a, o)), !r && o && o.empty.fire()
        }, _queueHooks: function (e, t) {
            var n = t + "queueHooks";
            return b._data(e, n) || b._data(e, n, {
                empty: b.Callbacks("once memory").add(function () {
                    b._removeData(e, t + "queue"), b._removeData(e, n)
                })
            })
        }
    }), b.fn.extend({
        queue: function (e, n) {
            var r = 2;
            return "string" != typeof e && (n = e, e = "fx", r--), r > arguments.length ? b.queue(this[0], e) : n === t ? this : this.each(function () {
                var t = b.queue(this, e, n);
                b._queueHooks(this, e), "fx" === e && "inprogress" !== t[0] && b.dequeue(this, e)
            })
        }, dequeue: function (e) {
            return this.each(function () {
                b.dequeue(this, e)
            })
        }, delay: function (e, t) {
            return e = b.fx ? b.fx.speeds[e] || e : e, t = t || "fx", this.queue(t, function (t, n) {
                var r = setTimeout(t, e);
                n.stop = function () {
                    clearTimeout(r)
                }
            })
        }, clearQueue: function (e) {
            return this.queue(e || "fx", [])
        }, promise: function (e, n) {
            var r, i = 1, o = b.Deferred(), a = this, s = this.length, u = function () {
                --i || o.resolveWith(a, [a])
            };
            "string" != typeof e && (n = e, e = t), e = e || "fx";
            while (s--) r = b._data(a[s], e + "queueHooks"), r && r.empty && (i++, r.empty.add(u));
            return u(), o.promise(n)
        }
    });
    var I, z, X = /[\t\r\n]/g, U = /\r/g, V = /^(?:input|select|textarea|button|object)$/i, Y = /^(?:a|area)$/i,
        J = /^(?:checked|selected|autofocus|autoplay|async|controls|defer|disabled|hidden|loop|multiple|open|readonly|required|scoped)$/i,
        G = /^(?:checked|selected)$/i, Q = b.support.getSetAttribute, K = b.support.input;
    b.fn.extend({
        attr: function (e, t) {
            return b.access(this, b.attr, e, t, arguments.length > 1)
        }, removeAttr: function (e) {
            return this.each(function () {
                b.removeAttr(this, e)
            })
        }, prop: function (e, t) {
            return b.access(this, b.prop, e, t, arguments.length > 1)
        }, removeProp: function (e) {
            return e = b.propFix[e] || e, this.each(function () {
                try {
                    this[e] = t, delete this[e]
                } catch (n) {
                }
            })
        }, addClass: function (e) {
            var t, n, r, i, o, a = 0, s = this.length, u = "string" == typeof e && e;
            if (b.isFunction(e)) return this.each(function (t) {
                b(this).addClass(e.call(this, t, this.className))
            });
            if (u) for (t = (e || "").match(w) || []; s > a; a++) if (n = this[a], r = 1 === n.nodeType && (n.className ? (" " + n.className + " ").replace(X, " ") : " ")) {
                o = 0;
                while (i = t[o++]) 0 > r.indexOf(" " + i + " ") && (r += i + " ");
                n.className = b.trim(r)
            }
            return this
        }, removeClass: function (e) {
            var t, n, r, i, o, a = 0, s = this.length, u = 0 === arguments.length || "string" == typeof e && e;
            if (b.isFunction(e)) return this.each(function (t) {
                b(this).removeClass(e.call(this, t, this.className))
            });
            if (u) for (t = (e || "").match(w) || []; s > a; a++) if (n = this[a], r = 1 === n.nodeType && (n.className ? (" " + n.className + " ").replace(X, " ") : "")) {
                o = 0;
                while (i = t[o++]) while (r.indexOf(" " + i + " ") >= 0) r = r.replace(" " + i + " ", " ");
                n.className = e ? b.trim(r) : ""
            }
            return this
        }, toggleClass: function (e, t) {
            var n = typeof e, r = "boolean" == typeof t;
            return b.isFunction(e) ? this.each(function (n) {
                b(this).toggleClass(e.call(this, n, this.className, t), t)
            }) : this.each(function () {
                if ("string" === n) {
                    var o, a = 0, s = b(this), u = t, l = e.match(w) || [];
                    while (o = l[a++]) u = r ? u : !s.hasClass(o), s[u ? "addClass" : "removeClass"](o)
                } else (n === i || "boolean" === n) && (this.className && b._data(this, "__className__", this.className), this.className = this.className || e === !1 ? "" : b._data(this, "__className__") || "")
            })
        }, hasClass: function (e) {
            var t = " " + e + " ", n = 0, r = this.length;
            for (; r > n; n++) if (1 === this[n].nodeType && (" " + this[n].className + " ").replace(X, " ").indexOf(t) >= 0) return !0;
            return !1
        }, val: function (e) {
            var n, r, i, o = this[0];
            {
                if (arguments.length) return i = b.isFunction(e), this.each(function (n) {
                    var o, a = b(this);
                    1 === this.nodeType && (o = i ? e.call(this, n, a.val()) : e, null == o ? o = "" : "number" == typeof o ? o += "" : b.isArray(o) && (o = b.map(o, function (e) {
                        return null == e ? "" : e + ""
                    })), r = b.valHooks[this.type] || b.valHooks[this.nodeName.toLowerCase()], r && "set" in r && r.set(this, o, "value") !== t || (this.value = o))
                });
                if (o) return r = b.valHooks[o.type] || b.valHooks[o.nodeName.toLowerCase()], r && "get" in r && (n = r.get(o, "value")) !== t ? n : (n = o.value, "string" == typeof n ? n.replace(U, "") : null == n ? "" : n)
            }
        }
    }), b.extend({
        valHooks: {
            option: {
                get: function (e) {
                    var t = e.attributes.value;
                    return !t || t.specified ? e.value : e.text
                }
            }, select: {
                get: function (e) {
                    var t, n, r = e.options, i = e.selectedIndex, o = "select-one" === e.type || 0 > i,
                        a = o ? null : [], s = o ? i + 1 : r.length, u = 0 > i ? s : o ? i : 0;
                    for (; s > u; u++) if (n = r[u], !(!n.selected && u !== i || (b.support.optDisabled ? n.disabled : null !== n.getAttribute("disabled")) || n.parentNode.disabled && b.nodeName(n.parentNode, "optgroup"))) {
                        if (t = b(n).val(), o) return t;
                        a.push(t)
                    }
                    return a
                }, set: function (e, t) {
                    var n = b.makeArray(t);
                    return b(e).find("option").each(function () {
                        this.selected = b.inArray(b(this).val(), n) >= 0
                    }), n.length || (e.selectedIndex = -1), n
                }
            }
        },
        attr: function (e, n, r) {
            var o, a, s, u = e.nodeType;
            if (e && 3 !== u && 8 !== u && 2 !== u) return typeof e.getAttribute === i ? b.prop(e, n, r) : (a = 1 !== u || !b.isXMLDoc(e), a && (n = n.toLowerCase(), o = b.attrHooks[n] || (J.test(n) ? z : I)), r === t ? o && a && "get" in o && null !== (s = o.get(e, n)) ? s : (typeof e.getAttribute !== i && (s = e.getAttribute(n)), null == s ? t : s) : null !== r ? o && a && "set" in o && (s = o.set(e, r, n)) !== t ? s : (e.setAttribute(n, r + ""), r) : (b.removeAttr(e, n), t))
        },
        removeAttr: function (e, t) {
            var n, r, i = 0, o = t && t.match(w);
            if (o && 1 === e.nodeType) while (n = o[i++]) r = b.propFix[n] || n, J.test(n) ? !Q && G.test(n) ? e[b.camelCase("default-" + n)] = e[r] = !1 : e[r] = !1 : b.attr(e, n, ""), e.removeAttribute(Q ? n : r)
        },
        attrHooks: {
            type: {
                set: function (e, t) {
                    if (!b.support.radioValue && "radio" === t && b.nodeName(e, "input")) {
                        var n = e.value;
                        return e.setAttribute("type", t), n && (e.value = n), t
                    }
                }
            }
        },
        propFix: {
            tabindex: "tabIndex",
            readonly: "readOnly",
            "for": "htmlFor",
            "class": "className",
            maxlength: "maxLength",
            cellspacing: "cellSpacing",
            cellpadding: "cellPadding",
            rowspan: "rowSpan",
            colspan: "colSpan",
            usemap: "useMap",
            frameborder: "frameBorder",
            contenteditable: "contentEditable"
        },
        prop: function (e, n, r) {
            var i, o, a, s = e.nodeType;
            if (e && 3 !== s && 8 !== s && 2 !== s) return a = 1 !== s || !b.isXMLDoc(e), a && (n = b.propFix[n] || n, o = b.propHooks[n]), r !== t ? o && "set" in o && (i = o.set(e, r, n)) !== t ? i : e[n] = r : o && "get" in o && null !== (i = o.get(e, n)) ? i : e[n]
        },
        propHooks: {
            tabIndex: {
                get: function (e) {
                    var n = e.getAttributeNode("tabindex");
                    return n && n.specified ? parseInt(n.value, 10) : V.test(e.nodeName) || Y.test(e.nodeName) && e.href ? 0 : t
                }
            }
        }
    }), z = {
        get: function (e, n) {
            var r = b.prop(e, n), i = "boolean" == typeof r && e.getAttribute(n),
                o = "boolean" == typeof r ? K && Q ? null != i : G.test(n) ? e[b.camelCase("default-" + n)] : !!i : e.getAttributeNode(n);
            return o && o.value !== !1 ? n.toLowerCase() : t
        }, set: function (e, t, n) {
            return t === !1 ? b.removeAttr(e, n) : K && Q || !G.test(n) ? e.setAttribute(!Q && b.propFix[n] || n, n) : e[b.camelCase("default-" + n)] = e[n] = !0, n
        }
    }, K && Q || (b.attrHooks.value = {
        get: function (e, n) {
            var r = e.getAttributeNode(n);
            return b.nodeName(e, "input") ? e.defaultValue : r && r.specified ? r.value : t
        }, set: function (e, n, r) {
            return b.nodeName(e, "input") ? (e.defaultValue = n, t) : I && I.set(e, n, r)
        }
    }), Q || (I = b.valHooks.button = {
        get: function (e, n) {
            var r = e.getAttributeNode(n);
            return r && ("id" === n || "name" === n || "coords" === n ? "" !== r.value : r.specified) ? r.value : t
        }, set: function (e, n, r) {
            var i = e.getAttributeNode(r);
            return i || e.setAttributeNode(i = e.ownerDocument.createAttribute(r)), i.value = n += "", "value" === r || n === e.getAttribute(r) ? n : t
        }
    }, b.attrHooks.contenteditable = {
        get: I.get, set: function (e, t, n) {
            I.set(e, "" === t ? !1 : t, n)
        }
    }, b.each(["width", "height"], function (e, n) {
        b.attrHooks[n] = b.extend(b.attrHooks[n], {
            set: function (e, r) {
                return "" === r ? (e.setAttribute(n, "auto"), r) : t
            }
        })
    })), b.support.hrefNormalized || (b.each(["href", "src", "width", "height"], function (e, n) {
        b.attrHooks[n] = b.extend(b.attrHooks[n], {
            get: function (e) {
                var r = e.getAttribute(n, 2);
                return null == r ? t : r
            }
        })
    }), b.each(["href", "src"], function (e, t) {
        b.propHooks[t] = {
            get: function (e) {
                return e.getAttribute(t, 4)
            }
        }
    })), b.support.style || (b.attrHooks.style = {
        get: function (e) {
            return e.style.cssText || t
        }, set: function (e, t) {
            return e.style.cssText = t + ""
        }
    }), b.support.optSelected || (b.propHooks.selected = b.extend(b.propHooks.selected, {
        get: function (e) {
            var t = e.parentNode;
            return t && (t.selectedIndex, t.parentNode && t.parentNode.selectedIndex), null
        }
    })), b.support.enctype || (b.propFix.enctype = "encoding"), b.support.checkOn || b.each(["radio", "checkbox"], function () {
        b.valHooks[this] = {
            get: function (e) {
                return null === e.getAttribute("value") ? "on" : e.value
            }
        }
    }), b.each(["radio", "checkbox"], function () {
        b.valHooks[this] = b.extend(b.valHooks[this], {
            set: function (e, n) {
                return b.isArray(n) ? e.checked = b.inArray(b(e).val(), n) >= 0 : t
            }
        })
    });
    var Z = /^(?:input|select|textarea)$/i, et = /^key/, tt = /^(?:mouse|contextmenu)|click/,
        nt = /^(?:focusinfocus|focusoutblur)$/, rt = /^([^.]*)(?:\.(.+)|)$/;

    function it() {
        return !0
    }

    function ot() {
        return !1
    }

    b.event = {
        global: {},
        add: function (e, n, r, o, a) {
            var s, u, l, c, p, f, d, h, g, m, y, v = b._data(e);
            if (v) {
                r.handler && (c = r, r = c.handler, a = c.selector), r.guid || (r.guid = b.guid++), (u = v.events) || (u = v.events = {}), (f = v.handle) || (f = v.handle = function (e) {
                    return typeof b === i || e && b.event.triggered === e.type ? t : b.event.dispatch.apply(f.elem, arguments)
                }, f.elem = e), n = (n || "").match(w) || [""], l = n.length;
                while (l--) s = rt.exec(n[l]) || [], g = y = s[1], m = (s[2] || "").split(".").sort(), p = b.event.special[g] || {}, g = (a ? p.delegateType : p.bindType) || g, p = b.event.special[g] || {}, d = b.extend({
                    type: g,
                    origType: y,
                    data: o,
                    handler: r,
                    guid: r.guid,
                    selector: a,
                    needsContext: a && b.expr.match.needsContext.test(a),
                    namespace: m.join(".")
                }, c), (h = u[g]) || (h = u[g] = [], h.delegateCount = 0, p.setup && p.setup.call(e, o, m, f) !== !1 || (e.addEventListener ? e.addEventListener(g, f, !1) : e.attachEvent && e.attachEvent("on" + g, f))), p.add && (p.add.call(e, d), d.handler.guid || (d.handler.guid = r.guid)), a ? h.splice(h.delegateCount++, 0, d) : h.push(d), b.event.global[g] = !0;
                e = null
            }
        },
        remove: function (e, t, n, r, i) {
            var o, a, s, u, l, c, p, f, d, h, g, m = b.hasData(e) && b._data(e);
            if (m && (c = m.events)) {
                t = (t || "").match(w) || [""], l = t.length;
                while (l--) if (s = rt.exec(t[l]) || [], d = g = s[1], h = (s[2] || "").split(".").sort(), d) {
                    p = b.event.special[d] || {}, d = (r ? p.delegateType : p.bindType) || d, f = c[d] || [], s = s[2] && RegExp("(^|\\.)" + h.join("\\.(?:.*\\.|)") + "(\\.|$)"), u = o = f.length;
                    while (o--) a = f[o], !i && g !== a.origType || n && n.guid !== a.guid || s && !s.test(a.namespace) || r && r !== a.selector && ("**" !== r || !a.selector) || (f.splice(o, 1), a.selector && f.delegateCount--, p.remove && p.remove.call(e, a));
                    u && !f.length && (p.teardown && p.teardown.call(e, h, m.handle) !== !1 || b.removeEvent(e, d, m.handle), delete c[d])
                } else for (d in c) b.event.remove(e, d + t[l], n, r, !0);
                b.isEmptyObject(c) && (delete m.handle, b._removeData(e, "events"))
            }
        },
        trigger: function (n, r, i, a) {
            var s, u, l, c, p, f, d, h = [i || o], g = y.call(n, "type") ? n.type : n,
                m = y.call(n, "namespace") ? n.namespace.split(".") : [];
            if (l = f = i = i || o, 3 !== i.nodeType && 8 !== i.nodeType && !nt.test(g + b.event.triggered) && (g.indexOf(".") >= 0 && (m = g.split("."), g = m.shift(), m.sort()), u = 0 > g.indexOf(":") && "on" + g, n = n[b.expando] ? n : new b.Event(g, "object" == typeof n && n), n.isTrigger = !0, n.namespace = m.join("."), n.namespace_re = n.namespace ? RegExp("(^|\\.)" + m.join("\\.(?:.*\\.|)") + "(\\.|$)") : null, n.result = t, n.target || (n.target = i), r = null == r ? [n] : b.makeArray(r, [n]), p = b.event.special[g] || {}, a || !p.trigger || p.trigger.apply(i, r) !== !1)) {
                if (!a && !p.noBubble && !b.isWindow(i)) {
                    for (c = p.delegateType || g, nt.test(c + g) || (l = l.parentNode); l; l = l.parentNode) h.push(l), f = l;
                    f === (i.ownerDocument || o) && h.push(f.defaultView || f.parentWindow || e)
                }
                d = 0;
                while ((l = h[d++]) && !n.isPropagationStopped()) n.type = d > 1 ? c : p.bindType || g, s = (b._data(l, "events") || {})[n.type] && b._data(l, "handle"), s && s.apply(l, r), s = u && l[u], s && b.acceptData(l) && s.apply && s.apply(l, r) === !1 && n.preventDefault();
                if (n.type = g, !(a || n.isDefaultPrevented() || p._default && p._default.apply(i.ownerDocument, r) !== !1 || "click" === g && b.nodeName(i, "a") || !b.acceptData(i) || !u || !i[g] || b.isWindow(i))) {
                    f = i[u], f && (i[u] = null), b.event.triggered = g;
                    try {
                        i[g]()
                    } catch (v) {
                    }
                    b.event.triggered = t, f && (i[u] = f)
                }
                return n.result
            }
        },
        dispatch: function (e) {
            e = b.event.fix(e);
            var n, r, i, o, a, s = [], u = h.call(arguments), l = (b._data(this, "events") || {})[e.type] || [],
                c = b.event.special[e.type] || {};
            if (u[0] = e, e.delegateTarget = this, !c.preDispatch || c.preDispatch.call(this, e) !== !1) {
                s = b.event.handlers.call(this, e, l), n = 0;
                while ((o = s[n++]) && !e.isPropagationStopped()) {
                    e.currentTarget = o.elem, a = 0;
                    while ((i = o.handlers[a++]) && !e.isImmediatePropagationStopped()) (!e.namespace_re || e.namespace_re.test(i.namespace)) && (e.handleObj = i, e.data = i.data, r = ((b.event.special[i.origType] || {}).handle || i.handler).apply(o.elem, u), r !== t && (e.result = r) === !1 && (e.preventDefault(), e.stopPropagation()))
                }
                return c.postDispatch && c.postDispatch.call(this, e), e.result
            }
        },
        handlers: function (e, n) {
            var r, i, o, a, s = [], u = n.delegateCount, l = e.target;
            if (u && l.nodeType && (!e.button || "click" !== e.type)) for (; l != this; l = l.parentNode || this) if (1 === l.nodeType && (l.disabled !== !0 || "click" !== e.type)) {
                for (o = [], a = 0; u > a; a++) i = n[a], r = i.selector + " ", o[r] === t && (o[r] = i.needsContext ? b(r, this).index(l) >= 0 : b.find(r, this, null, [l]).length), o[r] && o.push(i);
                o.length && s.push({elem: l, handlers: o})
            }
            return n.length > u && s.push({elem: this, handlers: n.slice(u)}), s
        },
        fix: function (e) {
            if (e[b.expando]) return e;
            var t, n, r, i = e.type, a = e, s = this.fixHooks[i];
            s || (this.fixHooks[i] = s = tt.test(i) ? this.mouseHooks : et.test(i) ? this.keyHooks : {}), r = s.props ? this.props.concat(s.props) : this.props, e = new b.Event(a), t = r.length;
            while (t--) n = r[t], e[n] = a[n];
            return e.target || (e.target = a.srcElement || o), 3 === e.target.nodeType && (e.target = e.target.parentNode), e.metaKey = !!e.metaKey, s.filter ? s.filter(e, a) : e
        },
        props: "altKey bubbles cancelable ctrlKey currentTarget eventPhase metaKey relatedTarget shiftKey target timeStamp view which".split(" "),
        fixHooks: {},
        keyHooks: {
            props: "char charCode key keyCode".split(" "), filter: function (e, t) {
                return null == e.which && (e.which = null != t.charCode ? t.charCode : t.keyCode), e
            }
        },
        mouseHooks: {
            props: "button buttons clientX clientY fromElement offsetX offsetY pageX pageY screenX screenY toElement".split(" "),
            filter: function (e, n) {
                var r, i, a, s = n.button, u = n.fromElement;
                return null == e.pageX && null != n.clientX && (i = e.target.ownerDocument || o, a = i.documentElement, r = i.body, e.pageX = n.clientX + (a && a.scrollLeft || r && r.scrollLeft || 0) - (a && a.clientLeft || r && r.clientLeft || 0), e.pageY = n.clientY + (a && a.scrollTop || r && r.scrollTop || 0) - (a && a.clientTop || r && r.clientTop || 0)), !e.relatedTarget && u && (e.relatedTarget = u === e.target ? n.toElement : u), e.which || s === t || (e.which = 1 & s ? 1 : 2 & s ? 3 : 4 & s ? 2 : 0), e
            }
        },
        special: {
            load: {noBubble: !0}, click: {
                trigger: function () {
                    return b.nodeName(this, "input") && "checkbox" === this.type && this.click ? (this.click(), !1) : t
                }
            }, focus: {
                trigger: function () {
                    if (this !== o.activeElement && this.focus) try {
                        return this.focus(), !1
                    } catch (e) {
                    }
                }, delegateType: "focusin"
            }, blur: {
                trigger: function () {
                    return this === o.activeElement && this.blur ? (this.blur(), !1) : t
                }, delegateType: "focusout"
            }, beforeunload: {
                postDispatch: function (e) {
                    e.result !== t && (e.originalEvent.returnValue = e.result)
                }
            }
        },
        simulate: function (e, t, n, r) {
            var i = b.extend(new b.Event, n, {type: e, isSimulated: !0, originalEvent: {}});
            r ? b.event.trigger(i, null, t) : b.event.dispatch.call(t, i), i.isDefaultPrevented() && n.preventDefault()
        }
    }, b.removeEvent = o.removeEventListener ? function (e, t, n) {
        e.removeEventListener && e.removeEventListener(t, n, !1)
    } : function (e, t, n) {
        var r = "on" + t;
        e.detachEvent && (typeof e[r] === i && (e[r] = null), e.detachEvent(r, n))
    }, b.Event = function (e, n) {
        return this instanceof b.Event ? (e && e.type ? (this.originalEvent = e, this.type = e.type, this.isDefaultPrevented = e.defaultPrevented || e.returnValue === !1 || e.getPreventDefault && e.getPreventDefault() ? it : ot) : this.type = e, n && b.extend(this, n), this.timeStamp = e && e.timeStamp || b.now(), this[b.expando] = !0, t) : new b.Event(e, n)
    }, b.Event.prototype = {
        isDefaultPrevented: ot,
        isPropagationStopped: ot,
        isImmediatePropagationStopped: ot,
        preventDefault: function () {
            var e = this.originalEvent;
            this.isDefaultPrevented = it, e && (e.preventDefault ? e.preventDefault() : e.returnValue = !1)
        },
        stopPropagation: function () {
            var e = this.originalEvent;
            this.isPropagationStopped = it, e && (e.stopPropagation && e.stopPropagation(), e.cancelBubble = !0)
        },
        stopImmediatePropagation: function () {
            this.isImmediatePropagationStopped = it, this.stopPropagation()
        }
    }, b.each({mouseenter: "mouseover", mouseleave: "mouseout"}, function (e, t) {
        b.event.special[e] = {
            delegateType: t, bindType: t, handle: function (e) {
                var n, r = this, i = e.relatedTarget, o = e.handleObj;
                return (!i || i !== r && !b.contains(r, i)) && (e.type = o.origType, n = o.handler.apply(this, arguments), e.type = t), n
            }
        }
    }), b.support.submitBubbles || (b.event.special.submit = {
        setup: function () {
            return b.nodeName(this, "form") ? !1 : (b.event.add(this, "click._submit keypress._submit", function (e) {
                var n = e.target, r = b.nodeName(n, "input") || b.nodeName(n, "button") ? n.form : t;
                r && !b._data(r, "submitBubbles") && (b.event.add(r, "submit._submit", function (e) {
                    e._submit_bubble = !0
                }), b._data(r, "submitBubbles", !0))
            }), t)
        }, postDispatch: function (e) {
            e._submit_bubble && (delete e._submit_bubble, this.parentNode && !e.isTrigger && b.event.simulate("submit", this.parentNode, e, !0))
        }, teardown: function () {
            return b.nodeName(this, "form") ? !1 : (b.event.remove(this, "._submit"), t)
        }
    }), b.support.changeBubbles || (b.event.special.change = {
        setup: function () {
            return Z.test(this.nodeName) ? (("checkbox" === this.type || "radio" === this.type) && (b.event.add(this, "propertychange._change", function (e) {
                "checked" === e.originalEvent.propertyName && (this._just_changed = !0)
            }), b.event.add(this, "click._change", function (e) {
                this._just_changed && !e.isTrigger && (this._just_changed = !1), b.event.simulate("change", this, e, !0)
            })), !1) : (b.event.add(this, "beforeactivate._change", function (e) {
                var t = e.target;
                Z.test(t.nodeName) && !b._data(t, "changeBubbles") && (b.event.add(t, "change._change", function (e) {
                    !this.parentNode || e.isSimulated || e.isTrigger || b.event.simulate("change", this.parentNode, e, !0)
                }), b._data(t, "changeBubbles", !0))
            }), t)
        }, handle: function (e) {
            var n = e.target;
            return this !== n || e.isSimulated || e.isTrigger || "radio" !== n.type && "checkbox" !== n.type ? e.handleObj.handler.apply(this, arguments) : t
        }, teardown: function () {
            return b.event.remove(this, "._change"), !Z.test(this.nodeName)
        }
    }), b.support.focusinBubbles || b.each({focus: "focusin", blur: "focusout"}, function (e, t) {
        var n = 0, r = function (e) {
            b.event.simulate(t, e.target, b.event.fix(e), !0)
        };
        b.event.special[t] = {
            setup: function () {
                0 === n++ && o.addEventListener(e, r, !0)
            }, teardown: function () {
                0 === --n && o.removeEventListener(e, r, !0)
            }
        }
    }), b.fn.extend({
        on: function (e, n, r, i, o) {
            var a, s;
            if ("object" == typeof e) {
                "string" != typeof n && (r = r || n, n = t);
                for (a in e) this.on(a, n, r, e[a], o);
                return this
            }
            if (null == r && null == i ? (i = n, r = n = t) : null == i && ("string" == typeof n ? (i = r, r = t) : (i = r, r = n, n = t)), i === !1) i = ot; else if (!i) return this;
            return 1 === o && (s = i, i = function (e) {
                return b().off(e), s.apply(this, arguments)
            }, i.guid = s.guid || (s.guid = b.guid++)), this.each(function () {
                b.event.add(this, e, i, r, n)
            })
        }, one: function (e, t, n, r) {
            return this.on(e, t, n, r, 1)
        }, off: function (e, n, r) {
            var i, o;
            if (e && e.preventDefault && e.handleObj) return i = e.handleObj, b(e.delegateTarget).off(i.namespace ? i.origType + "." + i.namespace : i.origType, i.selector, i.handler), this;
            if ("object" == typeof e) {
                for (o in e) this.off(o, n, e[o]);
                return this
            }
            return (n === !1 || "function" == typeof n) && (r = n, n = t), r === !1 && (r = ot), this.each(function () {
                b.event.remove(this, e, r, n)
            })
        }, bind: function (e, t, n) {
            return this.on(e, null, t, n)
        }, unbind: function (e, t) {
            return this.off(e, null, t)
        }, delegate: function (e, t, n, r) {
            return this.on(t, e, n, r)
        }, undelegate: function (e, t, n) {
            return 1 === arguments.length ? this.off(e, "**") : this.off(t, e || "**", n)
        }, trigger: function (e, t) {
            return this.each(function () {
                b.event.trigger(e, t, this)
            })
        }, triggerHandler: function (e, n) {
            var r = this[0];
            return r ? b.event.trigger(e, n, r, !0) : t
        }
    }), function (e, t) {
        var n, r, i, o, a, s, u, l, c, p, f, d, h, g, m, y, v, x = "sizzle" + -new Date, w = e.document, T = {}, N = 0,
            C = 0, k = it(), E = it(), S = it(), A = typeof t, j = 1 << 31, D = [], L = D.pop, H = D.push, q = D.slice,
            M = D.indexOf || function (e) {
                var t = 0, n = this.length;
                for (; n > t; t++) if (this[t] === e) return t;
                return -1
            }, _ = "[\\x20\\t\\r\\n\\f]", F = "(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+", O = F.replace("w", "w#"),
            B = "([*^$|!~]?=)",
            P = "\\[" + _ + "*(" + F + ")" + _ + "*(?:" + B + _ + "*(?:(['\"])((?:\\\\.|[^\\\\])*?)\\3|(" + O + ")|)|)" + _ + "*\\]",
            R = ":(" + F + ")(?:\\(((['\"])((?:\\\\.|[^\\\\])*?)\\3|((?:\\\\.|[^\\\\()[\\]]|" + P.replace(3, 8) + ")*)|.*)\\)|)",
            W = RegExp("^" + _ + "+|((?:^|[^\\\\])(?:\\\\.)*)" + _ + "+$", "g"), $ = RegExp("^" + _ + "*," + _ + "*"),
            I = RegExp("^" + _ + "*([\\x20\\t\\r\\n\\f>+~])" + _ + "*"), z = RegExp(R), X = RegExp("^" + O + "$"), U = {
                ID: RegExp("^#(" + F + ")"),
                CLASS: RegExp("^\\.(" + F + ")"),
                NAME: RegExp("^\\[name=['\"]?(" + F + ")['\"]?\\]"),
                TAG: RegExp("^(" + F.replace("w", "w*") + ")"),
                ATTR: RegExp("^" + P),
                PSEUDO: RegExp("^" + R),
                CHILD: RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\(" + _ + "*(even|odd|(([+-]|)(\\d*)n|)" + _ + "*(?:([+-]|)" + _ + "*(\\d+)|))" + _ + "*\\)|)", "i"),
                needsContext: RegExp("^" + _ + "*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\(" + _ + "*((?:-\\d)?\\d*)" + _ + "*\\)|)(?=[^-]|$)", "i")
            }, V = /[\x20\t\r\n\f]*[+~]/, Y = /^[^{]+\{\s*\[native code/, J = /^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,
            G = /^(?:input|select|textarea|button)$/i, Q = /^h\d$/i, K = /'|\\/g,
            Z = /\=[\x20\t\r\n\f]*([^'"\]]*)[\x20\t\r\n\f]*\]/g, et = /\\([\da-fA-F]{1,6}[\x20\t\r\n\f]?|.)/g,
            tt = function (e, t) {
                var n = "0x" + t - 65536;
                return n !== n ? t : 0 > n ? String.fromCharCode(n + 65536) : String.fromCharCode(55296 | n >> 10, 56320 | 1023 & n)
            };
        try {
            q.call(w.documentElement.childNodes, 0)[0].nodeType
        } catch (nt) {
            q = function (e) {
                var t, n = [];
                while (t = this[e++]) n.push(t);
                return n
            }
        }

        function rt(e) {
            return Y.test(e + "")
        }

        function it() {
            var e, t = [];
            return e = function (n, r) {
                return t.push(n += " ") > i.cacheLength && delete e[t.shift()], e[n] = r
            }
        }

        function ot(e) {
            return e[x] = !0, e
        }

        function at(e) {
            var t = p.createElement("div");
            try {
                return e(t)
            } catch (n) {
                return !1
            } finally {
                t = null
            }
        }

        function st(e, t, n, r) {
            var i, o, a, s, u, l, f, g, m, v;
            if ((t ? t.ownerDocument || t : w) !== p && c(t), t = t || p, n = n || [], !e || "string" != typeof e) return n;
            if (1 !== (s = t.nodeType) && 9 !== s) return [];
            if (!d && !r) {
                if (i = J.exec(e)) if (a = i[1]) {
                    if (9 === s) {
                        if (o = t.getElementById(a), !o || !o.parentNode) return n;
                        if (o.id === a) return n.push(o), n
                    } else if (t.ownerDocument && (o = t.ownerDocument.getElementById(a)) && y(t, o) && o.id === a) return n.push(o), n
                } else {
                    if (i[2]) return H.apply(n, q.call(t.getElementsByTagName(e), 0)), n;
                    if ((a = i[3]) && T.getByClassName && t.getElementsByClassName) return H.apply(n, q.call(t.getElementsByClassName(a), 0)), n
                }
                if (T.qsa && !h.test(e)) {
                    if (f = !0, g = x, m = t, v = 9 === s && e, 1 === s && "object" !== t.nodeName.toLowerCase()) {
                        l = ft(e), (f = t.getAttribute("id")) ? g = f.replace(K, "\\$&") : t.setAttribute("id", g), g = "[id='" + g + "'] ", u = l.length;
                        while (u--) l[u] = g + dt(l[u]);
                        m = V.test(e) && t.parentNode || t, v = l.join(",")
                    }
                    if (v) try {
                        return H.apply(n, q.call(m.querySelectorAll(v), 0)), n
                    } catch (b) {
                    } finally {
                        f || t.removeAttribute("id")
                    }
                }
            }
            return wt(e.replace(W, "$1"), t, n, r)
        }

        a = st.isXML = function (e) {
            var t = e && (e.ownerDocument || e).documentElement;
            return t ? "HTML" !== t.nodeName : !1
        }, c = st.setDocument = function (e) {
            var n = e ? e.ownerDocument || e : w;
            return n !== p && 9 === n.nodeType && n.documentElement ? (p = n, f = n.documentElement, d = a(n), T.tagNameNoComments = at(function (e) {
                return e.appendChild(n.createComment("")), !e.getElementsByTagName("*").length
            }), T.attributes = at(function (e) {
                e.innerHTML = "<select></select>";
                var t = typeof e.lastChild.getAttribute("multiple");
                return "boolean" !== t && "string" !== t
            }), T.getByClassName = at(function (e) {
                return e.innerHTML = "<div class='hidden e'></div><div class='hidden'></div>", e.getElementsByClassName && e.getElementsByClassName("e").length ? (e.lastChild.className = "e", 2 === e.getElementsByClassName("e").length) : !1
            }), T.getByName = at(function (e) {
                e.id = x + 0, e.innerHTML = "<a name='" + x + "'></a><div name='" + x + "'></div>", f.insertBefore(e, f.firstChild);
                var t = n.getElementsByName && n.getElementsByName(x).length === 2 + n.getElementsByName(x + 0).length;
                return T.getIdNotName = !n.getElementById(x), f.removeChild(e), t
            }), i.attrHandle = at(function (e) {
                return e.innerHTML = "<a href='#'></a>", e.firstChild && typeof e.firstChild.getAttribute !== A && "#" === e.firstChild.getAttribute("href")
            }) ? {} : {
                href: function (e) {
                    return e.getAttribute("href", 2)
                }, type: function (e) {
                    return e.getAttribute("type")
                }
            }, T.getIdNotName ? (i.find.ID = function (e, t) {
                if (typeof t.getElementById !== A && !d) {
                    var n = t.getElementById(e);
                    return n && n.parentNode ? [n] : []
                }
            }, i.filter.ID = function (e) {
                var t = e.replace(et, tt);
                return function (e) {
                    return e.getAttribute("id") === t
                }
            }) : (i.find.ID = function (e, n) {
                if (typeof n.getElementById !== A && !d) {
                    var r = n.getElementById(e);
                    return r ? r.id === e || typeof r.getAttributeNode !== A && r.getAttributeNode("id").value === e ? [r] : t : []
                }
            }, i.filter.ID = function (e) {
                var t = e.replace(et, tt);
                return function (e) {
                    var n = typeof e.getAttributeNode !== A && e.getAttributeNode("id");
                    return n && n.value === t
                }
            }), i.find.TAG = T.tagNameNoComments ? function (e, n) {
                return typeof n.getElementsByTagName !== A ? n.getElementsByTagName(e) : t
            } : function (e, t) {
                var n, r = [], i = 0, o = t.getElementsByTagName(e);
                if ("*" === e) {
                    while (n = o[i++]) 1 === n.nodeType && r.push(n);
                    return r
                }
                return o
            }, i.find.NAME = T.getByName && function (e, n) {
                return typeof n.getElementsByName !== A ? n.getElementsByName(name) : t
            }, i.find.CLASS = T.getByClassName && function (e, n) {
                return typeof n.getElementsByClassName === A || d ? t : n.getElementsByClassName(e)
            }, g = [], h = [":focus"], (T.qsa = rt(n.querySelectorAll)) && (at(function (e) {
                e.innerHTML = "<select><option selected=''></option></select>", e.querySelectorAll("[selected]").length || h.push("\\[" + _ + "*(?:checked|disabled|ismap|multiple|readonly|selected|value)"), e.querySelectorAll(":checked").length || h.push(":checked")
            }), at(function (e) {
                e.innerHTML = "<input type='hidden' i=''/>", e.querySelectorAll("[i^='']").length && h.push("[*^$]=" + _ + "*(?:\"\"|'')"), e.querySelectorAll(":enabled").length || h.push(":enabled", ":disabled"), e.querySelectorAll("*,:x"), h.push(",.*:")
            })), (T.matchesSelector = rt(m = f.matchesSelector || f.mozMatchesSelector || f.webkitMatchesSelector || f.oMatchesSelector || f.msMatchesSelector)) && at(function (e) {
                T.disconnectedMatch = m.call(e, "div"), m.call(e, "[s!='']:x"), g.push("!=", R)
            }), h = RegExp(h.join("|")), g = RegExp(g.join("|")), y = rt(f.contains) || f.compareDocumentPosition ? function (e, t) {
                var n = 9 === e.nodeType ? e.documentElement : e, r = t && t.parentNode;
                return e === r || !(!r || 1 !== r.nodeType || !(n.contains ? n.contains(r) : e.compareDocumentPosition && 16 & e.compareDocumentPosition(r)))
            } : function (e, t) {
                if (t) while (t = t.parentNode) if (t === e) return !0;
                return !1
            }, v = f.compareDocumentPosition ? function (e, t) {
                var r;
                return e === t ? (u = !0, 0) : (r = t.compareDocumentPosition && e.compareDocumentPosition && e.compareDocumentPosition(t)) ? 1 & r || e.parentNode && 11 === e.parentNode.nodeType ? e === n || y(w, e) ? -1 : t === n || y(w, t) ? 1 : 0 : 4 & r ? -1 : 1 : e.compareDocumentPosition ? -1 : 1
            } : function (e, t) {
                var r, i = 0, o = e.parentNode, a = t.parentNode, s = [e], l = [t];
                if (e === t) return u = !0, 0;
                if (!o || !a) return e === n ? -1 : t === n ? 1 : o ? -1 : a ? 1 : 0;
                if (o === a) return ut(e, t);
                r = e;
                while (r = r.parentNode) s.unshift(r);
                r = t;
                while (r = r.parentNode) l.unshift(r);
                while (s[i] === l[i]) i++;
                return i ? ut(s[i], l[i]) : s[i] === w ? -1 : l[i] === w ? 1 : 0
            }, u = !1, [0, 0].sort(v), T.detectDuplicates = u, p) : p
        }, st.matches = function (e, t) {
            return st(e, null, null, t)
        }, st.matchesSelector = function (e, t) {
            if ((e.ownerDocument || e) !== p && c(e), t = t.replace(Z, "='$1']"), !(!T.matchesSelector || d || g && g.test(t) || h.test(t))) try {
                var n = m.call(e, t);
                if (n || T.disconnectedMatch || e.document && 11 !== e.document.nodeType) return n
            } catch (r) {
            }
            return st(t, p, null, [e]).length > 0
        }, st.contains = function (e, t) {
            return (e.ownerDocument || e) !== p && c(e), y(e, t)
        }, st.attr = function (e, t) {
            var n;
            return (e.ownerDocument || e) !== p && c(e), d || (t = t.toLowerCase()), (n = i.attrHandle[t]) ? n(e) : d || T.attributes ? e.getAttribute(t) : ((n = e.getAttributeNode(t)) || e.getAttribute(t)) && e[t] === !0 ? t : n && n.specified ? n.value : null
        }, st.error = function (e) {
            throw Error("Syntax error, unrecognized expression: " + e)
        }, st.uniqueSort = function (e) {
            var t, n = [], r = 1, i = 0;
            if (u = !T.detectDuplicates, e.sort(v), u) {
                for (; t = e[r]; r++) t === e[r - 1] && (i = n.push(r));
                while (i--) e.splice(n[i], 1)
            }
            return e
        };

        function ut(e, t) {
            var n = t && e, r = n && (~t.sourceIndex || j) - (~e.sourceIndex || j);
            if (r) return r;
            if (n) while (n = n.nextSibling) if (n === t) return -1;
            return e ? 1 : -1
        }

        function lt(e) {
            return function (t) {
                var n = t.nodeName.toLowerCase();
                return "input" === n && t.type === e
            }
        }

        function ct(e) {
            return function (t) {
                var n = t.nodeName.toLowerCase();
                return ("input" === n || "button" === n) && t.type === e
            }
        }

        function pt(e) {
            return ot(function (t) {
                return t = +t, ot(function (n, r) {
                    var i, o = e([], n.length, t), a = o.length;
                    while (a--) n[i = o[a]] && (n[i] = !(r[i] = n[i]))
                })
            })
        }

        o = st.getText = function (e) {
            var t, n = "", r = 0, i = e.nodeType;
            if (i) {
                if (1 === i || 9 === i || 11 === i) {
                    if ("string" == typeof e.textContent) return e.textContent;
                    for (e = e.firstChild; e; e = e.nextSibling) n += o(e)
                } else if (3 === i || 4 === i) return e.nodeValue
            } else for (; t = e[r]; r++) n += o(t);
            return n
        }, i = st.selectors = {
            cacheLength: 50,
            createPseudo: ot,
            match: U,
            find: {},
            relative: {
                ">": {dir: "parentNode", first: !0},
                " ": {dir: "parentNode"},
                "+": {dir: "previousSibling", first: !0},
                "~": {dir: "previousSibling"}
            },
            preFilter: {
                ATTR: function (e) {
                    return e[1] = e[1].replace(et, tt), e[3] = (e[4] || e[5] || "").replace(et, tt), "~=" === e[2] && (e[3] = " " + e[3] + " "), e.slice(0, 4)
                }, CHILD: function (e) {
                    return e[1] = e[1].toLowerCase(), "nth" === e[1].slice(0, 3) ? (e[3] || st.error(e[0]), e[4] = +(e[4] ? e[5] + (e[6] || 1) : 2 * ("even" === e[3] || "odd" === e[3])), e[5] = +(e[7] + e[8] || "odd" === e[3])) : e[3] && st.error(e[0]), e
                }, PSEUDO: function (e) {
                    var t, n = !e[5] && e[2];
                    return U.CHILD.test(e[0]) ? null : (e[4] ? e[2] = e[4] : n && z.test(n) && (t = ft(n, !0)) && (t = n.indexOf(")", n.length - t) - n.length) && (e[0] = e[0].slice(0, t), e[2] = n.slice(0, t)), e.slice(0, 3))
                }
            },
            filter: {
                TAG: function (e) {
                    return "*" === e ? function () {
                        return !0
                    } : (e = e.replace(et, tt).toLowerCase(), function (t) {
                        return t.nodeName && t.nodeName.toLowerCase() === e
                    })
                }, CLASS: function (e) {
                    var t = k[e + " "];
                    return t || (t = RegExp("(^|" + _ + ")" + e + "(" + _ + "|$)")) && k(e, function (e) {
                        return t.test(e.className || typeof e.getAttribute !== A && e.getAttribute("class") || "")
                    })
                }, ATTR: function (e, t, n) {
                    return function (r) {
                        var i = st.attr(r, e);
                        return null == i ? "!=" === t : t ? (i += "", "=" === t ? i === n : "!=" === t ? i !== n : "^=" === t ? n && 0 === i.indexOf(n) : "*=" === t ? n && i.indexOf(n) > -1 : "$=" === t ? n && i.slice(-n.length) === n : "~=" === t ? (" " + i + " ").indexOf(n) > -1 : "|=" === t ? i === n || i.slice(0, n.length + 1) === n + "-" : !1) : !0
                    }
                }, CHILD: function (e, t, n, r, i) {
                    var o = "nth" !== e.slice(0, 3), a = "last" !== e.slice(-4), s = "of-type" === t;
                    return 1 === r && 0 === i ? function (e) {
                        return !!e.parentNode
                    } : function (t, n, u) {
                        var l, c, p, f, d, h, g = o !== a ? "nextSibling" : "previousSibling", m = t.parentNode,
                            y = s && t.nodeName.toLowerCase(), v = !u && !s;
                        if (m) {
                            if (o) {
                                while (g) {
                                    p = t;
                                    while (p = p[g]) if (s ? p.nodeName.toLowerCase() === y : 1 === p.nodeType) return !1;
                                    h = g = "only" === e && !h && "nextSibling"
                                }
                                return !0
                            }
                            if (h = [a ? m.firstChild : m.lastChild], a && v) {
                                c = m[x] || (m[x] = {}), l = c[e] || [], d = l[0] === N && l[1], f = l[0] === N && l[2], p = d && m.childNodes[d];
                                while (p = ++d && p && p[g] || (f = d = 0) || h.pop()) if (1 === p.nodeType && ++f && p === t) {
                                    c[e] = [N, d, f];
                                    break
                                }
                            } else if (v && (l = (t[x] || (t[x] = {}))[e]) && l[0] === N) f = l[1]; else while (p = ++d && p && p[g] || (f = d = 0) || h.pop()) if ((s ? p.nodeName.toLowerCase() === y : 1 === p.nodeType) && ++f && (v && ((p[x] || (p[x] = {}))[e] = [N, f]), p === t)) break;
                            return f -= i, f === r || 0 === f % r && f / r >= 0
                        }
                    }
                }, PSEUDO: function (e, t) {
                    var n, r = i.pseudos[e] || i.setFilters[e.toLowerCase()] || st.error("unsupported pseudo: " + e);
                    return r[x] ? r(t) : r.length > 1 ? (n = [e, e, "", t], i.setFilters.hasOwnProperty(e.toLowerCase()) ? ot(function (e, n) {
                        var i, o = r(e, t), a = o.length;
                        while (a--) i = M.call(e, o[a]), e[i] = !(n[i] = o[a])
                    }) : function (e) {
                        return r(e, 0, n)
                    }) : r
                }
            },
            pseudos: {
                not: ot(function (e) {
                    var t = [], n = [], r = s(e.replace(W, "$1"));
                    return r[x] ? ot(function (e, t, n, i) {
                        var o, a = r(e, null, i, []), s = e.length;
                        while (s--) (o = a[s]) && (e[s] = !(t[s] = o))
                    }) : function (e, i, o) {
                        return t[0] = e, r(t, null, o, n), !n.pop()
                    }
                }), has: ot(function (e) {
                    return function (t) {
                        return st(e, t).length > 0
                    }
                }), contains: ot(function (e) {
                    return function (t) {
                        return (t.textContent || t.innerText || o(t)).indexOf(e) > -1
                    }
                }), lang: ot(function (e) {
                    return X.test(e || "") || st.error("unsupported lang: " + e), e = e.replace(et, tt).toLowerCase(), function (t) {
                        var n;
                        do if (n = d ? t.getAttribute("xml:lang") || t.getAttribute("lang") : t.lang) return n = n.toLowerCase(), n === e || 0 === n.indexOf(e + "-"); while ((t = t.parentNode) && 1 === t.nodeType);
                        return !1
                    }
                }), target: function (t) {
                    var n = e.location && e.location.hash;
                    return n && n.slice(1) === t.id
                }, root: function (e) {
                    return e === f
                }, focus: function (e) {
                    return e === p.activeElement && (!p.hasFocus || p.hasFocus()) && !!(e.type || e.href || ~e.tabIndex)
                }, enabled: function (e) {
                    return e.disabled === !1
                }, disabled: function (e) {
                    return e.disabled === !0
                }, checked: function (e) {
                    var t = e.nodeName.toLowerCase();
                    return "input" === t && !!e.checked || "option" === t && !!e.selected
                }, selected: function (e) {
                    return e.parentNode && e.parentNode.selectedIndex, e.selected === !0
                }, empty: function (e) {
                    for (e = e.firstChild; e; e = e.nextSibling) if (e.nodeName > "@" || 3 === e.nodeType || 4 === e.nodeType) return !1;
                    return !0
                }, parent: function (e) {
                    return !i.pseudos.empty(e)
                }, header: function (e) {
                    return Q.test(e.nodeName)
                }, input: function (e) {
                    return G.test(e.nodeName)
                }, button: function (e) {
                    var t = e.nodeName.toLowerCase();
                    return "input" === t && "button" === e.type || "button" === t
                }, text: function (e) {
                    var t;
                    return "input" === e.nodeName.toLowerCase() && "text" === e.type && (null == (t = e.getAttribute("type")) || t.toLowerCase() === e.type)
                }, first: pt(function () {
                    return [0]
                }), last: pt(function (e, t) {
                    return [t - 1]
                }), eq: pt(function (e, t, n) {
                    return [0 > n ? n + t : n]
                }), even: pt(function (e, t) {
                    var n = 0;
                    for (; t > n; n += 2) e.push(n);
                    return e
                }), odd: pt(function (e, t) {
                    var n = 1;
                    for (; t > n; n += 2) e.push(n);
                    return e
                }), lt: pt(function (e, t, n) {
                    var r = 0 > n ? n + t : n;
                    for (; --r >= 0;) e.push(r);
                    return e
                }), gt: pt(function (e, t, n) {
                    var r = 0 > n ? n + t : n;
                    for (; t > ++r;) e.push(r);
                    return e
                })
            }
        };
        for (n in{radio: !0, checkbox: !0, file: !0, password: !0, image: !0}) i.pseudos[n] = lt(n);
        for (n in{submit: !0, reset: !0}) i.pseudos[n] = ct(n);

        function ft(e, t) {
            var n, r, o, a, s, u, l, c = E[e + " "];
            if (c) return t ? 0 : c.slice(0);
            s = e, u = [], l = i.preFilter;
            while (s) {
                (!n || (r = $.exec(s))) && (r && (s = s.slice(r[0].length) || s), u.push(o = [])), n = !1, (r = I.exec(s)) && (n = r.shift(), o.push({
                    value: n,
                    type: r[0].replace(W, " ")
                }), s = s.slice(n.length));
                for (a in i.filter) !(r = U[a].exec(s)) || l[a] && !(r = l[a](r)) || (n = r.shift(), o.push({
                    value: n,
                    type: a,
                    matches: r
                }), s = s.slice(n.length));
                if (!n) break
            }
            return t ? s.length : s ? st.error(e) : E(e, u).slice(0)
        }

        function dt(e) {
            var t = 0, n = e.length, r = "";
            for (; n > t; t++) r += e[t].value;
            return r
        }

        function ht(e, t, n) {
            var i = t.dir, o = n && "parentNode" === i, a = C++;
            return t.first ? function (t, n, r) {
                while (t = t[i]) if (1 === t.nodeType || o) return e(t, n, r)
            } : function (t, n, s) {
                var u, l, c, p = N + " " + a;
                if (s) {
                    while (t = t[i]) if ((1 === t.nodeType || o) && e(t, n, s)) return !0
                } else while (t = t[i]) if (1 === t.nodeType || o) if (c = t[x] || (t[x] = {}), (l = c[i]) && l[0] === p) {
                    if ((u = l[1]) === !0 || u === r) return u === !0
                } else if (l = c[i] = [p], l[1] = e(t, n, s) || r, l[1] === !0) return !0
            }
        }

        function gt(e) {
            return e.length > 1 ? function (t, n, r) {
                var i = e.length;
                while (i--) if (!e[i](t, n, r)) return !1;
                return !0
            } : e[0]
        }

        function mt(e, t, n, r, i) {
            var o, a = [], s = 0, u = e.length, l = null != t;
            for (; u > s; s++) (o = e[s]) && (!n || n(o, r, i)) && (a.push(o), l && t.push(s));
            return a
        }

        function yt(e, t, n, r, i, o) {
            return r && !r[x] && (r = yt(r)), i && !i[x] && (i = yt(i, o)), ot(function (o, a, s, u) {
                var l, c, p, f = [], d = [], h = a.length, g = o || xt(t || "*", s.nodeType ? [s] : s, []),
                    m = !e || !o && t ? g : mt(g, f, e, s, u), y = n ? i || (o ? e : h || r) ? [] : a : m;
                if (n && n(m, y, s, u), r) {
                    l = mt(y, d), r(l, [], s, u), c = l.length;
                    while (c--) (p = l[c]) && (y[d[c]] = !(m[d[c]] = p))
                }
                if (o) {
                    if (i || e) {
                        if (i) {
                            l = [], c = y.length;
                            while (c--) (p = y[c]) && l.push(m[c] = p);
                            i(null, y = [], l, u)
                        }
                        c = y.length;
                        while (c--) (p = y[c]) && (l = i ? M.call(o, p) : f[c]) > -1 && (o[l] = !(a[l] = p))
                    }
                } else y = mt(y === a ? y.splice(h, y.length) : y), i ? i(null, a, y, u) : H.apply(a, y)
            })
        }

        function vt(e) {
            var t, n, r, o = e.length, a = i.relative[e[0].type], s = a || i.relative[" "], u = a ? 1 : 0,
                c = ht(function (e) {
                    return e === t
                }, s, !0), p = ht(function (e) {
                    return M.call(t, e) > -1
                }, s, !0), f = [function (e, n, r) {
                    return !a && (r || n !== l) || ((t = n).nodeType ? c(e, n, r) : p(e, n, r))
                }];
            for (; o > u; u++) if (n = i.relative[e[u].type]) f = [ht(gt(f), n)]; else {
                if (n = i.filter[e[u].type].apply(null, e[u].matches), n[x]) {
                    for (r = ++u; o > r; r++) if (i.relative[e[r].type]) break;
                    return yt(u > 1 && gt(f), u > 1 && dt(e.slice(0, u - 1)).replace(W, "$1"), n, r > u && vt(e.slice(u, r)), o > r && vt(e = e.slice(r)), o > r && dt(e))
                }
                f.push(n)
            }
            return gt(f)
        }

        function bt(e, t) {
            var n = 0, o = t.length > 0, a = e.length > 0, s = function (s, u, c, f, d) {
                var h, g, m, y = [], v = 0, b = "0", x = s && [], w = null != d, T = l,
                    C = s || a && i.find.TAG("*", d && u.parentNode || u), k = N += null == T ? 1 : Math.random() || .1;
                for (w && (l = u !== p && u, r = n); null != (h = C[b]); b++) {
                    if (a && h) {
                        g = 0;
                        while (m = e[g++]) if (m(h, u, c)) {
                            f.push(h);
                            break
                        }
                        w && (N = k, r = ++n)
                    }
                    o && ((h = !m && h) && v--, s && x.push(h))
                }
                if (v += b, o && b !== v) {
                    g = 0;
                    while (m = t[g++]) m(x, y, u, c);
                    if (s) {
                        if (v > 0) while (b--) x[b] || y[b] || (y[b] = L.call(f));
                        y = mt(y)
                    }
                    H.apply(f, y), w && !s && y.length > 0 && v + t.length > 1 && st.uniqueSort(f)
                }
                return w && (N = k, l = T), x
            };
            return o ? ot(s) : s
        }

        s = st.compile = function (e, t) {
            var n, r = [], i = [], o = S[e + " "];
            if (!o) {
                t || (t = ft(e)), n = t.length;
                while (n--) o = vt(t[n]), o[x] ? r.push(o) : i.push(o);
                o = S(e, bt(i, r))
            }
            return o
        };

        function xt(e, t, n) {
            var r = 0, i = t.length;
            for (; i > r; r++) st(e, t[r], n);
            return n
        }

        function wt(e, t, n, r) {
            var o, a, u, l, c, p = ft(e);
            if (!r && 1 === p.length) {
                if (a = p[0] = p[0].slice(0), a.length > 2 && "ID" === (u = a[0]).type && 9 === t.nodeType && !d && i.relative[a[1].type]) {
                    if (t = i.find.ID(u.matches[0].replace(et, tt), t)[0], !t) return n;
                    e = e.slice(a.shift().value.length)
                }
                o = U.needsContext.test(e) ? 0 : a.length;
                while (o--) {
                    if (u = a[o], i.relative[l = u.type]) break;
                    if ((c = i.find[l]) && (r = c(u.matches[0].replace(et, tt), V.test(a[0].type) && t.parentNode || t))) {
                        if (a.splice(o, 1), e = r.length && dt(a), !e) return H.apply(n, q.call(r, 0)), n;
                        break
                    }
                }
            }
            return s(e, p)(r, t, d, n, V.test(e)), n
        }

        i.pseudos.nth = i.pseudos.eq;

        function Tt() {
        }

        i.filters = Tt.prototype = i.pseudos, i.setFilters = new Tt, c(), st.attr = b.attr, b.find = st, b.expr = st.selectors, b.expr[":"] = b.expr.pseudos, b.unique = st.uniqueSort, b.text = st.getText, b.isXMLDoc = st.isXML, b.contains = st.contains
    }(e);
    var at = /Until$/, st = /^(?:parents|prev(?:Until|All))/, ut = /^.[^:#\[\.,]*$/, lt = b.expr.match.needsContext,
        ct = {children: !0, contents: !0, next: !0, prev: !0};
    b.fn.extend({
        find: function (e) {
            var t, n, r, i = this.length;
            if ("string" != typeof e) return r = this, this.pushStack(b(e).filter(function () {
                for (t = 0; i > t; t++) if (b.contains(r[t], this)) return !0
            }));
            for (n = [], t = 0; i > t; t++) b.find(e, this[t], n);
            return n = this.pushStack(i > 1 ? b.unique(n) : n), n.selector = (this.selector ? this.selector + " " : "") + e, n
        }, has: function (e) {
            var t, n = b(e, this), r = n.length;
            return this.filter(function () {
                for (t = 0; r > t; t++) if (b.contains(this, n[t])) return !0
            })
        }, not: function (e) {
            return this.pushStack(ft(this, e, !1))
        }, filter: function (e) {
            return this.pushStack(ft(this, e, !0))
        }, is: function (e) {
            return !!e && ("string" == typeof e ? lt.test(e) ? b(e, this.context).index(this[0]) >= 0 : b.filter(e, this).length > 0 : this.filter(e).length > 0)
        }, closest: function (e, t) {
            var n, r = 0, i = this.length, o = [], a = lt.test(e) || "string" != typeof e ? b(e, t || this.context) : 0;
            for (; i > r; r++) {
                n = this[r];
                while (n && n.ownerDocument && n !== t && 11 !== n.nodeType) {
                    if (a ? a.index(n) > -1 : b.find.matchesSelector(n, e)) {
                        o.push(n);
                        break
                    }
                    n = n.parentNode
                }
            }
            return this.pushStack(o.length > 1 ? b.unique(o) : o)
        }, index: function (e) {
            return e ? "string" == typeof e ? b.inArray(this[0], b(e)) : b.inArray(e.jquery ? e[0] : e, this) : this[0] && this[0].parentNode ? this.first().prevAll().length : -1
        }, add: function (e, t) {
            var n = "string" == typeof e ? b(e, t) : b.makeArray(e && e.nodeType ? [e] : e), r = b.merge(this.get(), n);
            return this.pushStack(b.unique(r))
        }, addBack: function (e) {
            return this.add(null == e ? this.prevObject : this.prevObject.filter(e))
        }
    }), b.fn.andSelf = b.fn.addBack;

    function pt(e, t) {
        do e = e[t]; while (e && 1 !== e.nodeType);
        return e
    }

    b.each({
        parent: function (e) {
            var t = e.parentNode;
            return t && 11 !== t.nodeType ? t : null
        }, parents: function (e) {
            return b.dir(e, "parentNode")
        }, parentsUntil: function (e, t, n) {
            return b.dir(e, "parentNode", n)
        }, next: function (e) {
            return pt(e, "nextSibling")
        }, prev: function (e) {
            return pt(e, "previousSibling")
        }, nextAll: function (e) {
            return b.dir(e, "nextSibling")
        }, prevAll: function (e) {
            return b.dir(e, "previousSibling")
        }, nextUntil: function (e, t, n) {
            return b.dir(e, "nextSibling", n)
        }, prevUntil: function (e, t, n) {
            return b.dir(e, "previousSibling", n)
        }, siblings: function (e) {
            return b.sibling((e.parentNode || {}).firstChild, e)
        }, children: function (e) {
            return b.sibling(e.firstChild)
        }, contents: function (e) {
            return b.nodeName(e, "iframe") ? e.contentDocument || e.contentWindow.document : b.merge([], e.childNodes)
        }
    }, function (e, t) {
        b.fn[e] = function (n, r) {
            var i = b.map(this, t, n);
            return at.test(e) || (r = n), r && "string" == typeof r && (i = b.filter(r, i)), i = this.length > 1 && !ct[e] ? b.unique(i) : i, this.length > 1 && st.test(e) && (i = i.reverse()), this.pushStack(i)
        }
    }), b.extend({
        filter: function (e, t, n) {
            return n && (e = ":not(" + e + ")"), 1 === t.length ? b.find.matchesSelector(t[0], e) ? [t[0]] : [] : b.find.matches(e, t)
        }, dir: function (e, n, r) {
            var i = [], o = e[n];
            while (o && 9 !== o.nodeType && (r === t || 1 !== o.nodeType || !b(o).is(r))) 1 === o.nodeType && i.push(o), o = o[n];
            return i
        }, sibling: function (e, t) {
            var n = [];
            for (; e; e = e.nextSibling) 1 === e.nodeType && e !== t && n.push(e);
            return n
        }
    });

    function ft(e, t, n) {
        if (t = t || 0, b.isFunction(t)) return b.grep(e, function (e, r) {
            var i = !!t.call(e, r, e);
            return i === n
        });
        if (t.nodeType) return b.grep(e, function (e) {
            return e === t === n
        });
        if ("string" == typeof t) {
            var r = b.grep(e, function (e) {
                return 1 === e.nodeType
            });
            if (ut.test(t)) return b.filter(t, r, !n);
            t = b.filter(t, r)
        }
        return b.grep(e, function (e) {
            return b.inArray(e, t) >= 0 === n
        })
    }

    function dt(e) {
        var t = ht.split("|"), n = e.createDocumentFragment();
        if (n.createElement) while (t.length) n.createElement(t.pop());
        return n
    }

    var ht = "abbr|article|aside|audio|bdi|canvas|data|datalist|details|figcaption|figure|footer|header|hgroup|mark|meter|nav|output|progress|section|summary|time|video",
        gt = / jQuery\d+="(?:null|\d+)"/g, mt = RegExp("<(?:" + ht + ")[\\s/>]", "i"), yt = /^\s+/,
        vt = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/gi, bt = /<([\w:]+)/,
        xt = /<tbody/i, wt = /<|&#?\w+;/, Tt = /<(?:script|style|link)/i, Nt = /^(?:checkbox|radio)$/i,
        Ct = /checked\s*(?:[^=]|=\s*.checked.)/i, kt = /^$|\/(?:java|ecma)script/i, Et = /^true\/(.*)/,
        St = /^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g, At = {
            option: [1, "<select multiple='multiple'>", "</select>"],
            legend: [1, "<fieldset>", "</fieldset>"],
            area: [1, "<map>", "</map>"],
            param: [1, "<object>", "</object>"],
            thead: [1, "<table>", "</table>"],
            tr: [2, "<table><tbody>", "</tbody></table>"],
            col: [2, "<table><tbody></tbody><colgroup>", "</colgroup></table>"],
            td: [3, "<table><tbody><tr>", "</tr></tbody></table>"],
            _default: b.support.htmlSerialize ? [0, "", ""] : [1, "X<div>", "</div>"]
        }, jt = dt(o), Dt = jt.appendChild(o.createElement("div"));
    At.optgroup = At.option, At.tbody = At.tfoot = At.colgroup = At.caption = At.thead, At.th = At.td, b.fn.extend({
        text: function (e) {
            return b.access(this, function (e) {
                return e === t ? b.text(this) : this.empty().append((this[0] && this[0].ownerDocument || o).createTextNode(e))
            }, null, e, arguments.length)
        }, wrapAll: function (e) {
            if (b.isFunction(e)) return this.each(function (t) {
                b(this).wrapAll(e.call(this, t))
            });
            if (this[0]) {
                var t = b(e, this[0].ownerDocument).eq(0).clone(!0);
                this[0].parentNode && t.insertBefore(this[0]), t.map(function () {
                    var e = this;
                    while (e.firstChild && 1 === e.firstChild.nodeType) e = e.firstChild;
                    return e
                }).append(this)
            }
            return this
        }, wrapInner: function (e) {
            return b.isFunction(e) ? this.each(function (t) {
                b(this).wrapInner(e.call(this, t))
            }) : this.each(function () {
                var t = b(this), n = t.contents();
                n.length ? n.wrapAll(e) : t.append(e)
            })
        }, wrap: function (e) {
            var t = b.isFunction(e);
            return this.each(function (n) {
                b(this).wrapAll(t ? e.call(this, n) : e)
            })
        }, unwrap: function () {
            return this.parent().each(function () {
                b.nodeName(this, "body") || b(this).replaceWith(this.childNodes)
            }).end()
        }, append: function () {
            return this.domManip(arguments, !0, function (e) {
                (1 === this.nodeType || 11 === this.nodeType || 9 === this.nodeType) && this.appendChild(e)
            })
        }, prepend: function () {
            return this.domManip(arguments, !0, function (e) {
                (1 === this.nodeType || 11 === this.nodeType || 9 === this.nodeType) && this.insertBefore(e, this.firstChild)
            })
        }, before: function () {
            return this.domManip(arguments, !1, function (e) {
                this.parentNode && this.parentNode.insertBefore(e, this)
            })
        }, after: function () {
            return this.domManip(arguments, !1, function (e) {
                this.parentNode && this.parentNode.insertBefore(e, this.nextSibling)
            })
        }, remove: function (e, t) {
            var n, r = 0;
            for (; null != (n = this[r]); r++) (!e || b.filter(e, [n]).length > 0) && (t || 1 !== n.nodeType || b.cleanData(Ot(n)), n.parentNode && (t && b.contains(n.ownerDocument, n) && Mt(Ot(n, "script")), n.parentNode.removeChild(n)));
            return this
        }, empty: function () {
            var e, t = 0;
            for (; null != (e = this[t]); t++) {
                1 === e.nodeType && b.cleanData(Ot(e, !1));
                while (e.firstChild) e.removeChild(e.firstChild);
                e.options && b.nodeName(e, "select") && (e.options.length = 0)
            }
            return this
        }, clone: function (e, t) {
            return e = null == e ? !1 : e, t = null == t ? e : t, this.map(function () {
                return b.clone(this, e, t)
            })
        }, html: function (e) {
            return b.access(this, function (e) {
                var n = this[0] || {}, r = 0, i = this.length;
                if (e === t) return 1 === n.nodeType ? n.innerHTML.replace(gt, "") : t;
                if (!("string" != typeof e || Tt.test(e) || !b.support.htmlSerialize && mt.test(e) || !b.support.leadingWhitespace && yt.test(e) || At[(bt.exec(e) || ["", ""])[1].toLowerCase()])) {
                    e = e.replace(vt, "<$1></$2>");
                    try {
                        for (; i > r; r++) n = this[r] || {}, 1 === n.nodeType && (b.cleanData(Ot(n, !1)), n.innerHTML = e);
                        n = 0
                    } catch (o) {
                    }
                }
                n && this.empty().append(e)
            }, null, e, arguments.length)
        }, replaceWith: function (e) {
            var t = b.isFunction(e);
            return t || "string" == typeof e || (e = b(e).not(this).detach()), this.domManip([e], !0, function (e) {
                var t = this.nextSibling, n = this.parentNode;
                n && (b(this).remove(), n.insertBefore(e, t))
            })
        }, detach: function (e) {
            return this.remove(e, !0)
        }, domManip: function (e, n, r) {
            e = f.apply([], e);
            var i, o, a, s, u, l, c = 0, p = this.length, d = this, h = p - 1, g = e[0], m = b.isFunction(g);
            if (m || !(1 >= p || "string" != typeof g || b.support.checkClone) && Ct.test(g)) return this.each(function (i) {
                var o = d.eq(i);
                m && (e[0] = g.call(this, i, n ? o.html() : t)), o.domManip(e, n, r)
            });
            if (p && (l = b.buildFragment(e, this[0].ownerDocument, !1, this), i = l.firstChild, 1 === l.childNodes.length && (l = i), i)) {
                for (n = n && b.nodeName(i, "tr"), s = b.map(Ot(l, "script"), Ht), a = s.length; p > c; c++) o = l, c !== h && (o = b.clone(o, !0, !0), a && b.merge(s, Ot(o, "script"))), r.call(n && b.nodeName(this[c], "table") ? Lt(this[c], "tbody") : this[c], o, c);
                if (a) for (u = s[s.length - 1].ownerDocument, b.map(s, qt), c = 0; a > c; c++) o = s[c], kt.test(o.type || "") && !b._data(o, "globalEval") && b.contains(u, o) && (o.src ? b.ajax({
                    url: o.src,
                    type: "GET",
                    dataType: "script",
                    async: !1,
                    global: !1,
                    "throws": !0
                }) : b.globalEval((o.text || o.textContent || o.innerHTML || "").replace(St, "")));
                l = i = null
            }
            return this
        }
    });

    function Lt(e, t) {
        return e.getElementsByTagName(t)[0] || e.appendChild(e.ownerDocument.createElement(t))
    }

    function Ht(e) {
        var t = e.getAttributeNode("type");
        return e.type = (t && t.specified) + "/" + e.type, e
    }

    function qt(e) {
        var t = Et.exec(e.type);
        return t ? e.type = t[1] : e.removeAttribute("type"), e
    }

    function Mt(e, t) {
        var n, r = 0;
        for (; null != (n = e[r]); r++) b._data(n, "globalEval", !t || b._data(t[r], "globalEval"))
    }

    function _t(e, t) {
        if (1 === t.nodeType && b.hasData(e)) {
            var n, r, i, o = b._data(e), a = b._data(t, o), s = o.events;
            if (s) {
                delete a.handle, a.events = {};
                for (n in s) for (r = 0, i = s[n].length; i > r; r++) b.event.add(t, n, s[n][r])
            }
            a.data && (a.data = b.extend({}, a.data))
        }
    }

    function Ft(e, t) {
        var n, r, i;
        if (1 === t.nodeType) {
            if (n = t.nodeName.toLowerCase(), !b.support.noCloneEvent && t[b.expando]) {
                i = b._data(t);
                for (r in i.events) b.removeEvent(t, r, i.handle);
                t.removeAttribute(b.expando)
            }
            "script" === n && t.text !== e.text ? (Ht(t).text = e.text, qt(t)) : "object" === n ? (t.parentNode && (t.outerHTML = e.outerHTML), b.support.html5Clone && e.innerHTML && !b.trim(t.innerHTML) && (t.innerHTML = e.innerHTML)) : "input" === n && Nt.test(e.type) ? (t.defaultChecked = t.checked = e.checked, t.value !== e.value && (t.value = e.value)) : "option" === n ? t.defaultSelected = t.selected = e.defaultSelected : ("input" === n || "textarea" === n) && (t.defaultValue = e.defaultValue)
        }
    }

    b.each({
        appendTo: "append",
        prependTo: "prepend",
        insertBefore: "before",
        insertAfter: "after",
        replaceAll: "replaceWith"
    }, function (e, t) {
        b.fn[e] = function (e) {
            var n, r = 0, i = [], o = b(e), a = o.length - 1;
            for (; a >= r; r++) n = r === a ? this : this.clone(!0), b(o[r])[t](n), d.apply(i, n.get());
            return this.pushStack(i)
        }
    });

    function Ot(e, n) {
        var r, o, a = 0,
            s = typeof e.getElementsByTagName !== i ? e.getElementsByTagName(n || "*") : typeof e.querySelectorAll !== i ? e.querySelectorAll(n || "*") : t;
        if (!s) for (s = [], r = e.childNodes || e; null != (o = r[a]); a++) !n || b.nodeName(o, n) ? s.push(o) : b.merge(s, Ot(o, n));
        return n === t || n && b.nodeName(e, n) ? b.merge([e], s) : s
    }

    function Bt(e) {
        Nt.test(e.type) && (e.defaultChecked = e.checked)
    }

    b.extend({
        clone: function (e, t, n) {
            var r, i, o, a, s, u = b.contains(e.ownerDocument, e);
            if (b.support.html5Clone || b.isXMLDoc(e) || !mt.test("<" + e.nodeName + ">") ? o = e.cloneNode(!0) : (Dt.innerHTML = e.outerHTML, Dt.removeChild(o = Dt.firstChild)), !(b.support.noCloneEvent && b.support.noCloneChecked || 1 !== e.nodeType && 11 !== e.nodeType || b.isXMLDoc(e))) for (r = Ot(o), s = Ot(e), a = 0; null != (i = s[a]); ++a) r[a] && Ft(i, r[a]);
            if (t) if (n) for (s = s || Ot(e), r = r || Ot(o), a = 0; null != (i = s[a]); a++) _t(i, r[a]); else _t(e, o);
            return r = Ot(o, "script"), r.length > 0 && Mt(r, !u && Ot(e, "script")), r = s = i = null, o
        }, buildFragment: function (e, t, n, r) {
            var i, o, a, s, u, l, c, p = e.length, f = dt(t), d = [], h = 0;
            for (; p > h; h++) if (o = e[h], o || 0 === o) if ("object" === b.type(o)) b.merge(d, o.nodeType ? [o] : o); else if (wt.test(o)) {
                s = s || f.appendChild(t.createElement("div")), u = (bt.exec(o) || ["", ""])[1].toLowerCase(), c = At[u] || At._default, s.innerHTML = c[1] + o.replace(vt, "<$1></$2>") + c[2], i = c[0];
                while (i--) s = s.lastChild;
                if (!b.support.leadingWhitespace && yt.test(o) && d.push(t.createTextNode(yt.exec(o)[0])), !b.support.tbody) {
                    o = "table" !== u || xt.test(o) ? "<table>" !== c[1] || xt.test(o) ? 0 : s : s.firstChild, i = o && o.childNodes.length;
                    while (i--) b.nodeName(l = o.childNodes[i], "tbody") && !l.childNodes.length && o.removeChild(l)
                }
                b.merge(d, s.childNodes), s.textContent = "";
                while (s.firstChild) s.removeChild(s.firstChild);
                s = f.lastChild
            } else d.push(t.createTextNode(o));
            s && f.removeChild(s), b.support.appendChecked || b.grep(Ot(d, "input"), Bt), h = 0;
            while (o = d[h++]) if ((!r || -1 === b.inArray(o, r)) && (a = b.contains(o.ownerDocument, o), s = Ot(f.appendChild(o), "script"), a && Mt(s), n)) {
                i = 0;
                while (o = s[i++]) kt.test(o.type || "") && n.push(o)
            }
            return s = null, f
        }, cleanData: function (e, t) {
            var n, r, o, a, s = 0, u = b.expando, l = b.cache, p = b.support.deleteExpando, f = b.event.special;
            for (; null != (n = e[s]); s++) if ((t || b.acceptData(n)) && (o = n[u], a = o && l[o])) {
                if (a.events) for (r in a.events) f[r] ? b.event.remove(n, r) : b.removeEvent(n, r, a.handle);
                l[o] && (delete l[o], p ? delete n[u] : typeof n.removeAttribute !== i ? n.removeAttribute(u) : n[u] = null, c.push(o))
            }
        }
    });
    var Pt, Rt, Wt, $t = /alpha\([^)]*\)/i, It = /opacity\s*=\s*([^)]*)/, zt = /^(top|right|bottom|left)$/,
        Xt = /^(none|table(?!-c[ea]).+)/, Ut = /^margin/, Vt = RegExp("^(" + x + ")(.*)$", "i"),
        Yt = RegExp("^(" + x + ")(?!px)[a-z%]+$", "i"), Jt = RegExp("^([+-])=(" + x + ")", "i"), Gt = {BODY: "block"},
        Qt = {position: "absolute", visibility: "hidden", display: "block"}, Kt = {letterSpacing: 0, fontWeight: 400},
        Zt = ["Top", "Right", "Bottom", "Left"], en = ["Webkit", "O", "Moz", "ms"];

    function tn(e, t) {
        if (t in e) return t;
        var n = t.charAt(0).toUpperCase() + t.slice(1), r = t, i = en.length;
        while (i--) if (t = en[i] + n, t in e) return t;
        return r
    }

    function nn(e, t) {
        return e = t || e, "none" === b.css(e, "display") || !b.contains(e.ownerDocument, e)
    }

    function rn(e, t) {
        var n, r, i, o = [], a = 0, s = e.length;
        for (; s > a; a++) r = e[a], r.style && (o[a] = b._data(r, "olddisplay"), n = r.style.display, t ? (o[a] || "none" !== n || (r.style.display = ""), "" === r.style.display && nn(r) && (o[a] = b._data(r, "olddisplay", un(r.nodeName)))) : o[a] || (i = nn(r), (n && "none" !== n || !i) && b._data(r, "olddisplay", i ? n : b.css(r, "display"))));
        for (a = 0; s > a; a++) r = e[a], r.style && (t && "none" !== r.style.display && "" !== r.style.display || (r.style.display = t ? o[a] || "" : "none"));
        return e
    }

    b.fn.extend({
        css: function (e, n) {
            return b.access(this, function (e, n, r) {
                var i, o, a = {}, s = 0;
                if (b.isArray(n)) {
                    for (o = Rt(e), i = n.length; i > s; s++) a[n[s]] = b.css(e, n[s], !1, o);
                    return a
                }
                return r !== t ? b.style(e, n, r) : b.css(e, n)
            }, e, n, arguments.length > 1)
        }, show: function () {
            return rn(this, !0)
        }, hide: function () {
            return rn(this)
        }, toggle: function (e) {
            var t = "boolean" == typeof e;
            return this.each(function () {
                (t ? e : nn(this)) ? b(this).show() : b(this).hide()
            })
        }
    }), b.extend({
        cssHooks: {
            opacity: {
                get: function (e, t) {
                    if (t) {
                        var n = Wt(e, "opacity");
                        return "" === n ? "1" : n
                    }
                }
            }
        },
        cssNumber: {
            columnCount: !0,
            fillOpacity: !0,
            fontWeight: !0,
            lineHeight: !0,
            opacity: !0,
            orphans: !0,
            widows: !0,
            zIndex: !0,
            zoom: !0
        },
        cssProps: {"float": b.support.cssFloat ? "cssFloat" : "styleFloat"},
        style: function (e, n, r, i) {
            if (e && 3 !== e.nodeType && 8 !== e.nodeType && e.style) {
                var o, a, s, u = b.camelCase(n), l = e.style;
                if (n = b.cssProps[u] || (b.cssProps[u] = tn(l, u)), s = b.cssHooks[n] || b.cssHooks[u], r === t) return s && "get" in s && (o = s.get(e, !1, i)) !== t ? o : l[n];
                if (a = typeof r, "string" === a && (o = Jt.exec(r)) && (r = (o[1] + 1) * o[2] + parseFloat(b.css(e, n)), a = "number"), !(null == r || "number" === a && isNaN(r) || ("number" !== a || b.cssNumber[u] || (r += "px"), b.support.clearCloneStyle || "" !== r || 0 !== n.indexOf("background") || (l[n] = "inherit"), s && "set" in s && (r = s.set(e, r, i)) === t))) try {
                    l[n] = r
                } catch (c) {
                }
            }
        },
        css: function (e, n, r, i) {
            var o, a, s, u = b.camelCase(n);
            return n = b.cssProps[u] || (b.cssProps[u] = tn(e.style, u)), s = b.cssHooks[n] || b.cssHooks[u], s && "get" in s && (a = s.get(e, !0, r)), a === t && (a = Wt(e, n, i)), "normal" === a && n in Kt && (a = Kt[n]), "" === r || r ? (o = parseFloat(a), r === !0 || b.isNumeric(o) ? o || 0 : a) : a
        },
        swap: function (e, t, n, r) {
            var i, o, a = {};
            for (o in t) a[o] = e.style[o], e.style[o] = t[o];
            i = n.apply(e, r || []);
            for (o in t) e.style[o] = a[o];
            return i
        }
    }), e.getComputedStyle ? (Rt = function (t) {
        return e.getComputedStyle(t, null)
    }, Wt = function (e, n, r) {
        var i, o, a, s = r || Rt(e), u = s ? s.getPropertyValue(n) || s[n] : t, l = e.style;
        return s && ("" !== u || b.contains(e.ownerDocument, e) || (u = b.style(e, n)), Yt.test(u) && Ut.test(n) && (i = l.width, o = l.minWidth, a = l.maxWidth, l.minWidth = l.maxWidth = l.width = u, u = s.width, l.width = i, l.minWidth = o, l.maxWidth = a)), u
    }) : o.documentElement.currentStyle && (Rt = function (e) {
        return e.currentStyle
    }, Wt = function (e, n, r) {
        var i, o, a, s = r || Rt(e), u = s ? s[n] : t, l = e.style;
        return null == u && l && l[n] && (u = l[n]), Yt.test(u) && !zt.test(n) && (i = l.left, o = e.runtimeStyle, a = o && o.left, a && (o.left = e.currentStyle.left), l.left = "fontSize" === n ? "1em" : u, u = l.pixelLeft + "px", l.left = i, a && (o.left = a)), "" === u ? "auto" : u
    });

    function on(e, t, n) {
        var r = Vt.exec(t);
        return r ? Math.max(0, r[1] - (n || 0)) + (r[2] || "px") : t
    }

    function an(e, t, n, r, i) {
        var o = n === (r ? "border" : "content") ? 4 : "width" === t ? 1 : 0, a = 0;
        for (; 4 > o; o += 2) "margin" === n && (a += b.css(e, n + Zt[o], !0, i)), r ? ("content" === n && (a -= b.css(e, "padding" + Zt[o], !0, i)), "margin" !== n && (a -= b.css(e, "border" + Zt[o] + "Width", !0, i))) : (a += b.css(e, "padding" + Zt[o], !0, i), "padding" !== n && (a += b.css(e, "border" + Zt[o] + "Width", !0, i)));
        return a
    }

    function sn(e, t, n) {
        var r = !0, i = "width" === t ? e.offsetWidth : e.offsetHeight, o = Rt(e),
            a = b.support.boxSizing && "border-box" === b.css(e, "boxSizing", !1, o);
        if (0 >= i || null == i) {
            if (i = Wt(e, t, o), (0 > i || null == i) && (i = e.style[t]), Yt.test(i)) return i;
            r = a && (b.support.boxSizingReliable || i === e.style[t]), i = parseFloat(i) || 0
        }
        return i + an(e, t, n || (a ? "border" : "content"), r, o) + "px"
    }

    function un(e) {
        var t = o, n = Gt[e];
        return n || (n = ln(e, t), "none" !== n && n || (Pt = (Pt || b("<iframe frameborder='0' width='0' height='0'/>").css("cssText", "display:block !important")).appendTo(t.documentElement), t = (Pt[0].contentWindow || Pt[0].contentDocument).document, t.write("<!doctype html><html><body>"), t.close(), n = ln(e, t), Pt.detach()), Gt[e] = n), n
    }

    function ln(e, t) {
        var n = b(t.createElement(e)).appendTo(t.body), r = b.css(n[0], "display");
        return n.remove(), r
    }

    b.each(["height", "width"], function (e, n) {
        b.cssHooks[n] = {
            get: function (e, r, i) {
                return r ? 0 === e.offsetWidth && Xt.test(b.css(e, "display")) ? b.swap(e, Qt, function () {
                    return sn(e, n, i)
                }) : sn(e, n, i) : t
            }, set: function (e, t, r) {
                var i = r && Rt(e);
                return on(e, t, r ? an(e, n, r, b.support.boxSizing && "border-box" === b.css(e, "boxSizing", !1, i), i) : 0)
            }
        }
    }), b.support.opacity || (b.cssHooks.opacity = {
        get: function (e, t) {
            return It.test((t && e.currentStyle ? e.currentStyle.filter : e.style.filter) || "") ? .01 * parseFloat(RegExp.$1) + "" : t ? "1" : ""
        }, set: function (e, t) {
            var n = e.style, r = e.currentStyle, i = b.isNumeric(t) ? "alpha(opacity=" + 100 * t + ")" : "",
                o = r && r.filter || n.filter || "";
            n.zoom = 1, (t >= 1 || "" === t) && "" === b.trim(o.replace($t, "")) && n.removeAttribute && (n.removeAttribute("filter"), "" === t || r && !r.filter) || (n.filter = $t.test(o) ? o.replace($t, i) : o + " " + i)
        }
    }), b(function () {
        b.support.reliableMarginRight || (b.cssHooks.marginRight = {
            get: function (e, n) {
                return n ? b.swap(e, {display: "inline-block"}, Wt, [e, "marginRight"]) : t
            }
        }), !b.support.pixelPosition && b.fn.position && b.each(["top", "left"], function (e, n) {
            b.cssHooks[n] = {
                get: function (e, r) {
                    return r ? (r = Wt(e, n), Yt.test(r) ? b(e).position()[n] + "px" : r) : t
                }
            }
        })
    }), b.expr && b.expr.filters && (b.expr.filters.hidden = function (e) {
        return 0 >= e.offsetWidth && 0 >= e.offsetHeight || !b.support.reliableHiddenOffsets && "none" === (e.style && e.style.display || b.css(e, "display"))
    }, b.expr.filters.visible = function (e) {
        return !b.expr.filters.hidden(e)
    }), b.each({margin: "", padding: "", border: "Width"}, function (e, t) {
        b.cssHooks[e + t] = {
            expand: function (n) {
                var r = 0, i = {}, o = "string" == typeof n ? n.split(" ") : [n];
                for (; 4 > r; r++) i[e + Zt[r] + t] = o[r] || o[r - 2] || o[0];
                return i
            }
        }, Ut.test(e) || (b.cssHooks[e + t].set = on)
    });
    var cn = /%20/g, pn = /\[\]$/, fn = /\r?\n/g, dn = /^(?:submit|button|image|reset|file)$/i,
        hn = /^(?:input|select|textarea|keygen)/i;
    b.fn.extend({
        serialize: function () {
            return b.param(this.serializeArray())
        }, serializeArray: function () {
            return this.map(function () {
                var e = b.prop(this, "elements");
                return e ? b.makeArray(e) : this
            }).filter(function () {
                var e = this.type;
                return this.name && !b(this).is(":disabled") && hn.test(this.nodeName) && !dn.test(e) && (this.checked || !Nt.test(e))
            }).map(function (e, t) {
                var n = b(this).val();
                return null == n ? null : b.isArray(n) ? b.map(n, function (e) {
                    return {name: t.name, value: e.replace(fn, "\r\n")}
                }) : {name: t.name, value: n.replace(fn, "\r\n")}
            }).get()
        }
    }), b.param = function (e, n) {
        var r, i = [], o = function (e, t) {
            t = b.isFunction(t) ? t() : null == t ? "" : t, i[i.length] = encodeURIComponent(e) + "=" + encodeURIComponent(t)
        };
        if (n === t && (n = b.ajaxSettings && b.ajaxSettings.traditional), b.isArray(e) || e.jquery && !b.isPlainObject(e)) b.each(e, function () {
            o(this.name, this.value)
        }); else for (r in e) gn(r, e[r], n, o);
        return i.join("&").replace(cn, "+")
    };

    function gn(e, t, n, r) {
        var i;
        if (b.isArray(t)) b.each(t, function (t, i) {
            n || pn.test(e) ? r(e, i) : gn(e + "[" + ("object" == typeof i ? t : "") + "]", i, n, r)
        }); else if (n || "object" !== b.type(t)) r(e, t); else for (i in t) gn(e + "[" + i + "]", t[i], n, r)
    }

    b.each("blur focus focusin focusout load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup error contextmenu".split(" "), function (e, t) {
        b.fn[t] = function (e, n) {
            return arguments.length > 0 ? this.on(t, null, e, n) : this.trigger(t)
        }
    }), b.fn.hover = function (e, t) {
        return this.mouseenter(e).mouseleave(t || e)
    };
    var mn, yn, vn = b.now(), bn = /\?/, xn = /#.*$/, wn = /([?&])_=[^&]*/, Tn = /^(.*?):[ \t]*([^\r\n]*)\r?$/gm,
        Nn = /^(?:about|app|app-storage|.+-extension|file|res|widget):$/, Cn = /^(?:GET|HEAD)$/, kn = /^\/\//,
        En = /^([\w.+-]+:)(?:\/\/([^\/?#:]*)(?::(\d+)|)|)/, Sn = b.fn.load, An = {}, jn = {}, Dn = "*/".concat("*");
    try {
        yn = a.href
    } catch (Ln) {
        yn = o.createElement("a"), yn.href = "", yn = yn.href
    }
    mn = En.exec(yn.toLowerCase()) || [];

    function Hn(e) {
        return function (t, n) {
            "string" != typeof t && (n = t, t = "*");
            var r, i = 0, o = t.toLowerCase().match(w) || [];
            if (b.isFunction(n)) while (r = o[i++]) "+" === r[0] ? (r = r.slice(1) || "*", (e[r] = e[r] || []).unshift(n)) : (e[r] = e[r] || []).push(n)
        }
    }

    function qn(e, n, r, i) {
        var o = {}, a = e === jn;

        function s(u) {
            var l;
            return o[u] = !0, b.each(e[u] || [], function (e, u) {
                var c = u(n, r, i);
                return "string" != typeof c || a || o[c] ? a ? !(l = c) : t : (n.dataTypes.unshift(c), s(c), !1)
            }), l
        }

        return s(n.dataTypes[0]) || !o["*"] && s("*")
    }

    function Mn(e, n) {
        var r, i, o = b.ajaxSettings.flatOptions || {};
        for (i in n) n[i] !== t && ((o[i] ? e : r || (r = {}))[i] = n[i]);
        return r && b.extend(!0, e, r), e
    }

    b.fn.load = function (e, n, r) {
        if ("string" != typeof e && Sn) return Sn.apply(this, arguments);
        var i, o, a, s = this, u = e.indexOf(" ");
        return u >= 0 && (i = e.slice(u, e.length), e = e.slice(0, u)), b.isFunction(n) ? (r = n, n = t) : n && "object" == typeof n && (a = "POST"), s.length > 0 && b.ajax({
            url: e,
            type: a,
            dataType: "html",
            data: n
        }).done(function (e) {
            o = arguments, s.html(i ? b("<div>").append(b.parseHTML(e)).find(i) : e)
        }).complete(r && function (e, t) {
            s.each(r, o || [e.responseText, t, e])
        }), this
    }, b.each(["ajaxStart", "ajaxStop", "ajaxComplete", "ajaxError", "ajaxSuccess", "ajaxSend"], function (e, t) {
        b.fn[t] = function (e) {
            return this.on(t, e)
        }
    }), b.each(["get", "post"], function (e, n) {
        b[n] = function (e, r, i, o) {
            return b.isFunction(r) && (o = o || i, i = r, r = t), b.ajax({
                url: e,
                type: n,
                dataType: o,
                data: r,
                success: i
            })
        }
    }), b.extend({
        active: 0,
        lastModified: {},
        etag: {},
        ajaxSettings: {
            url: yn,
            type: "GET",
            isLocal: Nn.test(mn[1]),
            global: !0,
            processData: !0,
            async: !0,
            contentType: "application/x-www-form-urlencoded; charset=UTF-8",
            accepts: {
                "*": Dn,
                text: "text/plain",
                html: "text/html",
                xml: "application/xml, text/xml",
                json: "application/json, text/javascript"
            },
            contents: {xml: /xml/, html: /html/, json: /json/},
            responseFields: {xml: "responseXML", text: "responseText"},
            converters: {"* text": e.String, "text html": !0, "text json": b.parseJSON, "text xml": b.parseXML},
            flatOptions: {url: !0, context: !0}
        },
        ajaxSetup: function (e, t) {
            return t ? Mn(Mn(e, b.ajaxSettings), t) : Mn(b.ajaxSettings, e)
        },
        ajaxPrefilter: Hn(An),
        ajaxTransport: Hn(jn),
        ajax: function (e, n) {
            "object" == typeof e && (n = e, e = t), n = n || {};
            var r, i, o, a, s, u, l, c, p = b.ajaxSetup({}, n), f = p.context || p,
                d = p.context && (f.nodeType || f.jquery) ? b(f) : b.event, h = b.Deferred(),
                g = b.Callbacks("once memory"), m = p.statusCode || {}, y = {}, v = {}, x = 0, T = "canceled", N = {
                    readyState: 0, getResponseHeader: function (e) {
                        var t;
                        if (2 === x) {
                            if (!c) {
                                c = {};
                                while (t = Tn.exec(a)) c[t[1].toLowerCase()] = t[2]
                            }
                            t = c[e.toLowerCase()]
                        }
                        return null == t ? null : t
                    }, getAllResponseHeaders: function () {
                        return 2 === x ? a : null
                    }, setRequestHeader: function (e, t) {
                        var n = e.toLowerCase();
                        return x || (e = v[n] = v[n] || e, y[e] = t), this
                    }, overrideMimeType: function (e) {
                        return x || (p.mimeType = e), this
                    }, statusCode: function (e) {
                        var t;
                        if (e) if (2 > x) for (t in e) m[t] = [m[t], e[t]]; else N.always(e[N.status]);
                        return this
                    }, abort: function (e) {
                        var t = e || T;
                        return l && l.abort(t), k(0, t), this
                    }
                };
            if (h.promise(N).complete = g.add, N.success = N.done, N.error = N.fail, p.url = ((e || p.url || yn) + "").replace(xn, "").replace(kn, mn[1] + "//"), p.type = n.method || n.type || p.method || p.type, p.dataTypes = b.trim(p.dataType || "*").toLowerCase().match(w) || [""], null == p.crossDomain && (r = En.exec(p.url.toLowerCase()), p.crossDomain = !(!r || r[1] === mn[1] && r[2] === mn[2] && (r[3] || ("http:" === r[1] ? 80 : 443)) == (mn[3] || ("http:" === mn[1] ? 80 : 443)))), p.data && p.processData && "string" != typeof p.data && (p.data = b.param(p.data, p.traditional)), qn(An, p, n, N), 2 === x) return N;
            u = p.global, u && 0 === b.active++ && b.event.trigger("ajaxStart"), p.type = p.type.toUpperCase(), p.hasContent = !Cn.test(p.type), o = p.url, p.hasContent || (p.data && (o = p.url += (bn.test(o) ? "&" : "?") + p.data, delete p.data), p.cache === !1 && (p.url = wn.test(o) ? o.replace(wn, "$1_=" + vn++) : o + (bn.test(o) ? "&" : "?") + "_=" + vn++)), p.ifModified && (b.lastModified[o] && N.setRequestHeader("If-Modified-Since", b.lastModified[o]), b.etag[o] && N.setRequestHeader("If-None-Match", b.etag[o])), (p.data && p.hasContent && p.contentType !== !1 || n.contentType) && N.setRequestHeader("Content-Type", p.contentType), N.setRequestHeader("Accept", p.dataTypes[0] && p.accepts[p.dataTypes[0]] ? p.accepts[p.dataTypes[0]] + ("*" !== p.dataTypes[0] ? ", " + Dn + "; q=0.01" : "") : p.accepts["*"]);
            for (i in p.headers) N.setRequestHeader(i, p.headers[i]);
            if (p.beforeSend && (p.beforeSend.call(f, N, p) === !1 || 2 === x)) return N.abort();
            T = "abort";
            for (i in{success: 1, error: 1, complete: 1}) N[i](p[i]);
            if (l = qn(jn, p, n, N)) {
                N.readyState = 1, u && d.trigger("ajaxSend", [N, p]), p.async && p.timeout > 0 && (s = setTimeout(function () {
                    N.abort("timeout")
                }, p.timeout));
                try {
                    x = 1, l.send(y, k)
                } catch (C) {
                    if (!(2 > x)) throw C;
                    k(-1, C)
                }
            } else k(-1, "No Transport");

            function k(e, n, r, i) {
                var c, y, v, w, T, C = n;
                2 !== x && (x = 2, s && clearTimeout(s), l = t, a = i || "", N.readyState = e > 0 ? 4 : 0, r && (w = _n(p, N, r)), e >= 200 && 300 > e || 304 === e ? (p.ifModified && (T = N.getResponseHeader("Last-Modified"), T && (b.lastModified[o] = T), T = N.getResponseHeader("etag"), T && (b.etag[o] = T)), 204 === e ? (c = !0, C = "nocontent") : 304 === e ? (c = !0, C = "notmodified") : (c = Fn(p, w), C = c.state, y = c.data, v = c.error, c = !v)) : (v = C, (e || !C) && (C = "error", 0 > e && (e = 0))), N.status = e, N.statusText = (n || C) + "", c ? h.resolveWith(f, [y, C, N]) : h.rejectWith(f, [N, C, v]), N.statusCode(m), m = t, u && d.trigger(c ? "ajaxSuccess" : "ajaxError", [N, p, c ? y : v]), g.fireWith(f, [N, C]), u && (d.trigger("ajaxComplete", [N, p]), --b.active || b.event.trigger("ajaxStop")))
            }

            return N
        },
        getScript: function (e, n) {
            return b.get(e, t, n, "script")
        },
        getJSON: function (e, t, n) {
            return b.get(e, t, n, "json")
        }
    });

    function _n(e, n, r) {
        var i, o, a, s, u = e.contents, l = e.dataTypes, c = e.responseFields;
        for (s in c) s in r && (n[c[s]] = r[s]);
        while ("*" === l[0]) l.shift(), o === t && (o = e.mimeType || n.getResponseHeader("Content-Type"));
        if (o) for (s in u) if (u[s] && u[s].test(o)) {
            l.unshift(s);
            break
        }
        if (l[0] in r) a = l[0]; else {
            for (s in r) {
                if (!l[0] || e.converters[s + " " + l[0]]) {
                    a = s;
                    break
                }
                i || (i = s)
            }
            a = a || i
        }
        return a ? (a !== l[0] && l.unshift(a), r[a]) : t
    }

    function Fn(e, t) {
        var n, r, i, o, a = {}, s = 0, u = e.dataTypes.slice(), l = u[0];
        if (e.dataFilter && (t = e.dataFilter(t, e.dataType)), u[1]) for (i in e.converters) a[i.toLowerCase()] = e.converters[i];
        for (; r = u[++s];) if ("*" !== r) {
            if ("*" !== l && l !== r) {
                if (i = a[l + " " + r] || a["* " + r], !i) for (n in a) if (o = n.split(" "), o[1] === r && (i = a[l + " " + o[0]] || a["* " + o[0]])) {
                    i === !0 ? i = a[n] : a[n] !== !0 && (r = o[0], u.splice(s--, 0, r));
                    break
                }
                if (i !== !0) if (i && e["throws"]) t = i(t); else try {
                    t = i(t)
                } catch (c) {
                    return {state: "parsererror", error: i ? c : "No conversion from " + l + " to " + r}
                }
            }
            l = r
        }
        return {state: "success", data: t}
    }

    b.ajaxSetup({
        accepts: {script: "text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"},
        contents: {script: /(?:java|ecma)script/},
        converters: {
            "text script": function (e) {
                return b.globalEval(e), e
            }
        }
    }), b.ajaxPrefilter("script", function (e) {
        e.cache === t && (e.cache = !1), e.crossDomain && (e.type = "GET", e.global = !1)
    }), b.ajaxTransport("script", function (e) {
        if (e.crossDomain) {
            var n, r = o.head || b("head")[0] || o.documentElement;
            return {
                send: function (t, i) {
                    n = o.createElement("script"), n.async = !0, e.scriptCharset && (n.charset = e.scriptCharset), n.src = e.url, n.onload = n.onreadystatechange = function (e, t) {
                        (t || !n.readyState || /loaded|complete/.test(n.readyState)) && (n.onload = n.onreadystatechange = null, n.parentNode && n.parentNode.removeChild(n), n = null, t || i(200, "success"))
                    }, r.insertBefore(n, r.firstChild)
                }, abort: function () {
                    n && n.onload(t, !0)
                }
            }
        }
    });
    var On = [], Bn = /(=)\?(?=&|$)|\?\?/;
    b.ajaxSetup({
        jsonp: "callback", jsonpCallback: function () {
            var e = On.pop() || b.expando + "_" + vn++;
            return this[e] = !0, e
        }
    }), b.ajaxPrefilter("json jsonp", function (n, r, i) {
        var o, a, s,
            u = n.jsonp !== !1 && (Bn.test(n.url) ? "url" : "string" == typeof n.data && !(n.contentType || "").indexOf("application/x-www-form-urlencoded") && Bn.test(n.data) && "data");
        return u || "jsonp" === n.dataTypes[0] ? (o = n.jsonpCallback = b.isFunction(n.jsonpCallback) ? n.jsonpCallback() : n.jsonpCallback, u ? n[u] = n[u].replace(Bn, "$1" + o) : n.jsonp !== !1 && (n.url += (bn.test(n.url) ? "&" : "?") + n.jsonp + "=" + o), n.converters["script json"] = function () {
            return s || b.error(o + " was not called"), s[0]
        }, n.dataTypes[0] = "json", a = e[o], e[o] = function () {
            s = arguments
        }, i.always(function () {
            e[o] = a, n[o] && (n.jsonpCallback = r.jsonpCallback, On.push(o)), s && b.isFunction(a) && a(s[0]), s = a = t
        }), "script") : t
    });
    var Pn, Rn, Wn = 0, $n = e.ActiveXObject && function () {
        var e;
        for (e in Pn) Pn[e](t, !0)
    };

    function In() {
        try {
            return new e.XMLHttpRequest
        } catch (t) {
        }
    }

    function zn() {
        try {
            return new e.ActiveXObject("Microsoft.XMLHTTP")
        } catch (t) {
        }
    }

    b.ajaxSettings.xhr = e.ActiveXObject ? function () {
        return !this.isLocal && In() || zn()
    } : In, Rn = b.ajaxSettings.xhr(), b.support.cors = !!Rn && "withCredentials" in Rn, Rn = b.support.ajax = !!Rn, Rn && b.ajaxTransport(function (n) {
        if (!n.crossDomain || b.support.cors) {
            var r;
            return {
                send: function (i, o) {
                    var a, s, u = n.xhr();
                    if (n.username ? u.open(n.type, n.url, n.async, n.username, n.password) : u.open(n.type, n.url, n.async), n.xhrFields) for (s in n.xhrFields) u[s] = n.xhrFields[s];
                    n.mimeType && u.overrideMimeType && u.overrideMimeType(n.mimeType), n.crossDomain || i["X-Requested-With"] || (i["X-Requested-With"] = "XMLHttpRequest");
                    try {
                        for (s in i) u.setRequestHeader(s, i[s])
                    } catch (l) {
                    }
                    u.send(n.hasContent && n.data || null), r = function (e, i) {
                        var s, l, c, p;
                        try {
                            if (r && (i || 4 === u.readyState)) if (r = t, a && (u.onreadystatechange = b.noop, $n && delete Pn[a]), i) 4 !== u.readyState && u.abort(); else {
                                p = {}, s = u.status, l = u.getAllResponseHeaders(), "string" == typeof u.responseText && (p.text = u.responseText);
                                try {
                                    c = u.statusText
                                } catch (f) {
                                    c = ""
                                }
                                s || !n.isLocal || n.crossDomain ? 1223 === s && (s = 204) : s = p.text ? 200 : 404
                            }
                        } catch (d) {
                            i || o(-1, d)
                        }
                        p && o(s, c, p, l)
                    }, n.async ? 4 === u.readyState ? setTimeout(r) : (a = ++Wn, $n && (Pn || (Pn = {}, b(e).unload($n)), Pn[a] = r), u.onreadystatechange = r) : r()
                }, abort: function () {
                    r && r(t, !0)
                }
            }
        }
    });
    var Xn, Un, Vn = /^(?:toggle|show|hide)$/, Yn = RegExp("^(?:([+-])=|)(" + x + ")([a-z%]*)$", "i"),
        Jn = /queueHooks$/, Gn = [nr], Qn = {
            "*": [function (e, t) {
                var n, r, i = this.createTween(e, t), o = Yn.exec(t), a = i.cur(), s = +a || 0, u = 1, l = 20;
                if (o) {
                    if (n = +o[2], r = o[3] || (b.cssNumber[e] ? "" : "px"), "px" !== r && s) {
                        s = b.css(i.elem, e, !0) || n || 1;
                        do u = u || ".5", s /= u, b.style(i.elem, e, s + r); while (u !== (u = i.cur() / a) && 1 !== u && --l)
                    }
                    i.unit = r, i.start = s, i.end = o[1] ? s + (o[1] + 1) * n : n
                }
                return i
            }]
        };

    function Kn() {
        return setTimeout(function () {
            Xn = t
        }), Xn = b.now()
    }

    function Zn(e, t) {
        b.each(t, function (t, n) {
            var r = (Qn[t] || []).concat(Qn["*"]), i = 0, o = r.length;
            for (; o > i; i++) if (r[i].call(e, t, n)) return
        })
    }

    function er(e, t, n) {
        var r, i, o = 0, a = Gn.length, s = b.Deferred().always(function () {
            delete u.elem
        }), u = function () {
            if (i) return !1;
            var t = Xn || Kn(), n = Math.max(0, l.startTime + l.duration - t), r = n / l.duration || 0, o = 1 - r,
                a = 0, u = l.tweens.length;
            for (; u > a; a++) l.tweens[a].run(o);
            return s.notifyWith(e, [l, o, n]), 1 > o && u ? n : (s.resolveWith(e, [l]), !1)
        }, l = s.promise({
            elem: e,
            props: b.extend({}, t),
            opts: b.extend(!0, {specialEasing: {}}, n),
            originalProperties: t,
            originalOptions: n,
            startTime: Xn || Kn(),
            duration: n.duration,
            tweens: [],
            createTween: function (t, n) {
                var r = b.Tween(e, l.opts, t, n, l.opts.specialEasing[t] || l.opts.easing);
                return l.tweens.push(r), r
            },
            stop: function (t) {
                var n = 0, r = t ? l.tweens.length : 0;
                if (i) return this;
                for (i = !0; r > n; n++) l.tweens[n].run(1);
                return t ? s.resolveWith(e, [l, t]) : s.rejectWith(e, [l, t]), this
            }
        }), c = l.props;
        for (tr(c, l.opts.specialEasing); a > o; o++) if (r = Gn[o].call(l, e, c, l.opts)) return r;
        return Zn(l, c), b.isFunction(l.opts.start) && l.opts.start.call(e, l), b.fx.timer(b.extend(u, {
            elem: e,
            anim: l,
            queue: l.opts.queue
        })), l.progress(l.opts.progress).done(l.opts.done, l.opts.complete).fail(l.opts.fail).always(l.opts.always)
    }

    function tr(e, t) {
        var n, r, i, o, a;
        for (i in e) if (r = b.camelCase(i), o = t[r], n = e[i], b.isArray(n) && (o = n[1], n = e[i] = n[0]), i !== r && (e[r] = n, delete e[i]), a = b.cssHooks[r], a && "expand" in a) {
            n = a.expand(n), delete e[r];
            for (i in n) i in e || (e[i] = n[i], t[i] = o)
        } else t[r] = o
    }

    b.Animation = b.extend(er, {
        tweener: function (e, t) {
            b.isFunction(e) ? (t = e, e = ["*"]) : e = e.split(" ");
            var n, r = 0, i = e.length;
            for (; i > r; r++) n = e[r], Qn[n] = Qn[n] || [], Qn[n].unshift(t)
        }, prefilter: function (e, t) {
            t ? Gn.unshift(e) : Gn.push(e)
        }
    });

    function nr(e, t, n) {
        var r, i, o, a, s, u, l, c, p, f = this, d = e.style, h = {}, g = [], m = e.nodeType && nn(e);
        n.queue || (c = b._queueHooks(e, "fx"), null == c.unqueued && (c.unqueued = 0, p = c.empty.fire, c.empty.fire = function () {
            c.unqueued || p()
        }), c.unqueued++, f.always(function () {
            f.always(function () {
                c.unqueued--, b.queue(e, "fx").length || c.empty.fire()
            })
        })), 1 === e.nodeType && ("height" in t || "width" in t) && (n.overflow = [d.overflow, d.overflowX, d.overflowY], "inline" === b.css(e, "display") && "none" === b.css(e, "float") && (b.support.inlineBlockNeedsLayout && "inline" !== un(e.nodeName) ? d.zoom = 1 : d.display = "inline-block")), n.overflow && (d.overflow = "hidden", b.support.shrinkWrapBlocks || f.always(function () {
            d.overflow = n.overflow[0], d.overflowX = n.overflow[1], d.overflowY = n.overflow[2]
        }));
        for (i in t) if (a = t[i], Vn.exec(a)) {
            if (delete t[i], u = u || "toggle" === a, a === (m ? "hide" : "show")) continue;
            g.push(i)
        }
        if (o = g.length) {
            s = b._data(e, "fxshow") || b._data(e, "fxshow", {}), "hidden" in s && (m = s.hidden), u && (s.hidden = !m), m ? b(e).show() : f.done(function () {
                b(e).hide()
            }), f.done(function () {
                var t;
                b._removeData(e, "fxshow");
                for (t in h) b.style(e, t, h[t])
            });
            for (i = 0; o > i; i++) r = g[i], l = f.createTween(r, m ? s[r] : 0), h[r] = s[r] || b.style(e, r), r in s || (s[r] = l.start, m && (l.end = l.start, l.start = "width" === r || "height" === r ? 1 : 0))
        }
    }

    function rr(e, t, n, r, i) {
        return new rr.prototype.init(e, t, n, r, i)
    }

    b.Tween = rr, rr.prototype = {
        constructor: rr, init: function (e, t, n, r, i, o) {
            this.elem = e, this.prop = n, this.easing = i || "swing", this.options = t, this.start = this.now = this.cur(), this.end = r, this.unit = o || (b.cssNumber[n] ? "" : "px")
        }, cur: function () {
            var e = rr.propHooks[this.prop];
            return e && e.get ? e.get(this) : rr.propHooks._default.get(this)
        }, run: function (e) {
            var t, n = rr.propHooks[this.prop];
            return this.pos = t = this.options.duration ? b.easing[this.easing](e, this.options.duration * e, 0, 1, this.options.duration) : e, this.now = (this.end - this.start) * t + this.start, this.options.step && this.options.step.call(this.elem, this.now, this), n && n.set ? n.set(this) : rr.propHooks._default.set(this), this
        }
    }, rr.prototype.init.prototype = rr.prototype, rr.propHooks = {
        _default: {
            get: function (e) {
                var t;
                return null == e.elem[e.prop] || e.elem.style && null != e.elem.style[e.prop] ? (t = b.css(e.elem, e.prop, ""), t && "auto" !== t ? t : 0) : e.elem[e.prop]
            }, set: function (e) {
                b.fx.step[e.prop] ? b.fx.step[e.prop](e) : e.elem.style && (null != e.elem.style[b.cssProps[e.prop]] || b.cssHooks[e.prop]) ? b.style(e.elem, e.prop, e.now + e.unit) : e.elem[e.prop] = e.now
            }
        }
    }, rr.propHooks.scrollTop = rr.propHooks.scrollLeft = {
        set: function (e) {
            e.elem.nodeType && e.elem.parentNode && (e.elem[e.prop] = e.now)
        }
    }, b.each(["toggle", "show", "hide"], function (e, t) {
        var n = b.fn[t];
        b.fn[t] = function (e, r, i) {
            return null == e || "boolean" == typeof e ? n.apply(this, arguments) : this.animate(ir(t, !0), e, r, i)
        }
    }), b.fn.extend({
        fadeTo: function (e, t, n, r) {
            return this.filter(nn).css("opacity", 0).show().end().animate({opacity: t}, e, n, r)
        }, animate: function (e, t, n, r) {
            var i = b.isEmptyObject(e), o = b.speed(t, n, r), a = function () {
                var t = er(this, b.extend({}, e), o);
                a.finish = function () {
                    t.stop(!0)
                }, (i || b._data(this, "finish")) && t.stop(!0)
            };
            return a.finish = a, i || o.queue === !1 ? this.each(a) : this.queue(o.queue, a)
        }, stop: function (e, n, r) {
            var i = function (e) {
                var t = e.stop;
                delete e.stop, t(r)
            };
            return "string" != typeof e && (r = n, n = e, e = t), n && e !== !1 && this.queue(e || "fx", []), this.each(function () {
                var t = !0, n = null != e && e + "queueHooks", o = b.timers, a = b._data(this);
                if (n) a[n] && a[n].stop && i(a[n]); else for (n in a) a[n] && a[n].stop && Jn.test(n) && i(a[n]);
                for (n = o.length; n--;) o[n].elem !== this || null != e && o[n].queue !== e || (o[n].anim.stop(r), t = !1, o.splice(n, 1));
                (t || !r) && b.dequeue(this, e)
            })
        }, finish: function (e) {
            return e !== !1 && (e = e || "fx"), this.each(function () {
                var t, n = b._data(this), r = n[e + "queue"], i = n[e + "queueHooks"], o = b.timers,
                    a = r ? r.length : 0;
                for (n.finish = !0, b.queue(this, e, []), i && i.cur && i.cur.finish && i.cur.finish.call(this), t = o.length; t--;) o[t].elem === this && o[t].queue === e && (o[t].anim.stop(!0), o.splice(t, 1));
                for (t = 0; a > t; t++) r[t] && r[t].finish && r[t].finish.call(this);
                delete n.finish
            })
        }
    });

    function ir(e, t) {
        var n, r = {height: e}, i = 0;
        for (t = t ? 1 : 0; 4 > i; i += 2 - t) n = Zt[i], r["margin" + n] = r["padding" + n] = e;
        return t && (r.opacity = r.width = e), r
    }

    b.each({
        slideDown: ir("show"),
        slideUp: ir("hide"),
        slideToggle: ir("toggle"),
        fadeIn: {opacity: "show"},
        fadeOut: {opacity: "hide"},
        fadeToggle: {opacity: "toggle"}
    }, function (e, t) {
        b.fn[e] = function (e, n, r) {
            return this.animate(t, e, n, r)
        }
    }), b.speed = function (e, t, n) {
        var r = e && "object" == typeof e ? b.extend({}, e) : {
            complete: n || !n && t || b.isFunction(e) && e,
            duration: e,
            easing: n && t || t && !b.isFunction(t) && t
        };
        return r.duration = b.fx.off ? 0 : "number" == typeof r.duration ? r.duration : r.duration in b.fx.speeds ? b.fx.speeds[r.duration] : b.fx.speeds._default, (null == r.queue || r.queue === !0) && (r.queue = "fx"), r.old = r.complete, r.complete = function () {
            b.isFunction(r.old) && r.old.call(this), r.queue && b.dequeue(this, r.queue)
        }, r
    }, b.easing = {
        linear: function (e) {
            return e
        }, swing: function (e) {
            return .5 - Math.cos(e * Math.PI) / 2
        }
    }, b.timers = [], b.fx = rr.prototype.init, b.fx.tick = function () {
        var e, n = b.timers, r = 0;
        for (Xn = b.now(); n.length > r; r++) e = n[r], e() || n[r] !== e || n.splice(r--, 1);
        n.length || b.fx.stop(), Xn = t
    }, b.fx.timer = function (e) {
        e() && b.timers.push(e) && b.fx.start()
    }, b.fx.interval = 13, b.fx.start = function () {
        Un || (Un = setInterval(b.fx.tick, b.fx.interval))
    }, b.fx.stop = function () {
        clearInterval(Un), Un = null
    }, b.fx.speeds = {
        slow: 600,
        fast: 200,
        _default: 400
    }, b.fx.step = {}, b.expr && b.expr.filters && (b.expr.filters.animated = function (e) {
        return b.grep(b.timers, function (t) {
            return e === t.elem
        }).length
    }), b.fn.offset = function (e) {
        if (arguments.length) return e === t ? this : this.each(function (t) {
            b.offset.setOffset(this, e, t)
        });
        var n, r, o = {top: 0, left: 0}, a = this[0], s = a && a.ownerDocument;
        if (s) return n = s.documentElement, b.contains(n, a) ? (typeof a.getBoundingClientRect !== i && (o = a.getBoundingClientRect()), r = or(s), {
            top: o.top + (r.pageYOffset || n.scrollTop) - (n.clientTop || 0),
            left: o.left + (r.pageXOffset || n.scrollLeft) - (n.clientLeft || 0)
        }) : o
    }, b.offset = {
        setOffset: function (e, t, n) {
            var r = b.css(e, "position");
            "static" === r && (e.style.position = "relative");
            var i = b(e), o = i.offset(), a = b.css(e, "top"), s = b.css(e, "left"),
                u = ("absolute" === r || "fixed" === r) && b.inArray("auto", [a, s]) > -1, l = {}, c = {}, p, f;
            u ? (c = i.position(), p = c.top, f = c.left) : (p = parseFloat(a) || 0, f = parseFloat(s) || 0), b.isFunction(t) && (t = t.call(e, n, o)), null != t.top && (l.top = t.top - o.top + p), null != t.left && (l.left = t.left - o.left + f), "using" in t ? t.using.call(e, l) : i.css(l)
        }
    }, b.fn.extend({
        position: function () {
            if (this[0]) {
                var e, t, n = {top: 0, left: 0}, r = this[0];
                return "fixed" === b.css(r, "position") ? t = r.getBoundingClientRect() : (e = this.offsetParent(), t = this.offset(), b.nodeName(e[0], "html") || (n = e.offset()), n.top += b.css(e[0], "borderTopWidth", !0), n.left += b.css(e[0], "borderLeftWidth", !0)), {
                    top: t.top - n.top - b.css(r, "marginTop", !0),
                    left: t.left - n.left - b.css(r, "marginLeft", !0)
                }
            }
        }, offsetParent: function () {
            return this.map(function () {
                var e = this.offsetParent || o.documentElement;
                while (e && !b.nodeName(e, "html") && "static" === b.css(e, "position")) e = e.offsetParent;
                return e || o.documentElement
            })
        }
    }), b.each({scrollLeft: "pageXOffset", scrollTop: "pageYOffset"}, function (e, n) {
        var r = /Y/.test(n);
        b.fn[e] = function (i) {
            return b.access(this, function (e, i, o) {
                var a = or(e);
                return o === t ? a ? n in a ? a[n] : a.document.documentElement[i] : e[i] : (a ? a.scrollTo(r ? b(a).scrollLeft() : o, r ? o : b(a).scrollTop()) : e[i] = o, t)
            }, e, i, arguments.length, null)
        }
    });

    function or(e) {
        return b.isWindow(e) ? e : 9 === e.nodeType ? e.defaultView || e.parentWindow : !1
    }

    b.each({Height: "height", Width: "width"}, function (e, n) {
        b.each({padding: "inner" + e, content: n, "": "outer" + e}, function (r, i) {
            b.fn[i] = function (i, o) {
                var a = arguments.length && (r || "boolean" != typeof i),
                    s = r || (i === !0 || o === !0 ? "margin" : "border");
                return b.access(this, function (n, r, i) {
                    var o;
                    return b.isWindow(n) ? n.document.documentElement["client" + e] : 9 === n.nodeType ? (o = n.documentElement, Math.max(n.body["scroll" + e], o["scroll" + e], n.body["offset" + e], o["offset" + e], o["client" + e])) : i === t ? b.css(n, r, s) : b.style(n, r, i, s)
                }, n, a ? i : t, a, null)
            }
        })
    }), e.jQuery = e.$ = b, "function" == typeof define && define.amd && define.amd.jQuery && define("jquery", [], function () {
        return b
    })
})(window);
/*!
 * better-scroll v0.4.0
 * (c) 2016-2017 ustbhuangyi
 * Released under the MIT License.
 */
(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
        typeof define === 'function' && define.amd ? define(factory) :
            (global.BScroll = factory());
}(this, (function () {
    'use strict';

    var elementStyle = document.createElement('div').style;

    var vendor = function () {
        var transformNames = {
            webkit: 'webkitTransform',
            Moz: 'MozTransform',
            O: 'OTransform',
            ms: 'msTransform',
            standard: 'transform'
        };

        for (var key in transformNames) {
            if (elementStyle[transformNames[key]] !== undefined) {
                return key;
            }
        }

        return false;
    }();

    function prefixStyle(style) {
        if (vendor === false) {
            return false;
        }

        if (vendor === 'standard') {
            return style;
        }

        return vendor + style.charAt(0).toUpperCase() + style.substr(1);
    }

    function addEvent(el, type, fn, capture) {
        el.addEventListener(type, fn, {passive: false, capture: !!capture});
    }

    function removeEvent(el, type, fn, capture) {
        el.removeEventListener(type, fn, !!capture);
    }

    function offset(el) {
        var left = 0;
        var top = 0;

        while (el) {
            left -= el.offsetLeft;
            top -= el.offsetTop;
            el = el.offsetParent;
        }

        return {
            left: left,
            top: top
        };
    }

    var transform = prefixStyle('transform');

    var hasPerspective = prefixStyle('perspective') in elementStyle;
    var hasTouch = 'ontouchstart' in window;
    var hasTransform = transform !== false;
    var hasTransition = prefixStyle('transition') in elementStyle;

    var style = {
        transform: transform,
        transitionTimingFunction: prefixStyle('transitionTimingFunction'),
        transitionDuration: prefixStyle('transitionDuration'),
        transitionDelay: prefixStyle('transitionDelay'),
        transformOrigin: prefixStyle('transformOrigin'),
        transitionEnd: prefixStyle('transitionEnd')
    };

    var TOUCH_EVENT$1 = 1;
    var MOUSE_EVENT = 2;
    var eventType = {
        touchstart: TOUCH_EVENT$1,
        touchmove: TOUCH_EVENT$1,
        touchend: TOUCH_EVENT$1,

        mousedown: MOUSE_EVENT,
        mousemove: MOUSE_EVENT,
        mouseup: MOUSE_EVENT
    };

    function getRect(el) {
        if (el instanceof window.SVGElement) {
            var rect = el.getBoundingClientRect();
            return {
                top: rect.top,
                left: rect.left,
                width: rect.width,
                height: rect.height
            };
        } else {
            return {
                top: el.offsetTop,
                left: el.offsetLeft,
                width: el.offsetWidth,
                height: el.offsetHeight
            };
        }
    }

    function preventDefaultException(el, exceptions) {
        for (var i in exceptions) {
            if (exceptions[i].test(el[i])) {
                return true;
            }
        }
        return false;
    }

    function tap(e, eventName) {
        var ev = document.createEvent('Event');
        ev.initEvent(eventName, true, true);
        ev.pageX = e.pageX;
        ev.pageY = e.pageY;
        e.target.dispatchEvent(ev);
    }

    function click(e) {
        var target = e.target;

        if (!/(SELECT|INPUT|TEXTAREA)/i.test(target.tagName)) {
            var ev = document.createEvent(window.MouseEvent ? 'MouseEvents' : 'Event');
            ev.initEvent('click', true, true);
            ev._constructed = true;
            target.dispatchEvent(ev);
        }
    }

    function prepend(el, target) {
        if (target.firstChild) {
            before(el, target.firstChild);
        } else {
            target.appendChild(el);
        }
    }

    function before(el, target) {
        target.parentNode.insertBefore(el, target);
    }

    function extend(target, source) {
        for (var key in source) {
            target[key] = source[key];
        }
    }

    var DEFAULT_INTERVAL = 100 / 60;

    var requestAnimationFrame = function () {
        return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame ||
            // if all else fails, use setTimeout
            function (callback) {
                return window.setTimeout(callback, (callback.interval || DEFAULT_INTERVAL) / 2); // make interval as precise as possible.
            };
    }();

    var cancelAnimationFrame = function () {
        return window.cancelAnimationFrame || window.webkitCancelAnimationFrame || window.mozCancelAnimationFrame || window.oCancelAnimationFrame || function (id) {
            window.clearTimeout(id);
        };
    }();

    var isBadAndroid = /Android /.test(window.navigator.appVersion) && !/Chrome\/\d/.test(window.navigator.appVersion);

    var ease = {
        // easeOutQuint
        swipe: {
            style: 'cubic-bezier(0.23, 1, 0.32, 1)',
            fn: function fn(t) {
                return 1 + --t * t * t * t * t;
            }
        },
        // easeOutQuard
        swipeBounce: {
            style: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
            fn: function fn(t) {
                return t * (2 - t);
            }
        },
        // easeOutQuart
        bounce: {
            style: 'cubic-bezier(0.165, 0.84, 0.44, 1)',
            fn: function fn(t) {
                return 1 - --t * t * t * t;
            }
        }
    };

    var classCallCheck = function (instance, Constructor) {
        if (!(instance instanceof Constructor)) {
            throw new TypeError("Cannot call a class as a function");
        }
    };

    var createClass = function () {
        function defineProperties(target, props) {
            for (var i = 0; i < props.length; i++) {
                var descriptor = props[i];
                descriptor.enumerable = descriptor.enumerable || false;
                descriptor.configurable = true;
                if ("value" in descriptor) descriptor.writable = true;
                Object.defineProperty(target, descriptor.key, descriptor);
            }
        }

        return function (Constructor, protoProps, staticProps) {
            if (protoProps) defineProperties(Constructor.prototype, protoProps);
            if (staticProps) defineProperties(Constructor, staticProps);
            return Constructor;
        };
    }();


    var inherits = function (subClass, superClass) {
        if (typeof superClass !== "function" && superClass !== null) {
            throw new TypeError("Super expression must either be null or a function, not " + typeof superClass);
        }

        subClass.prototype = Object.create(superClass && superClass.prototype, {
            constructor: {
                value: subClass,
                enumerable: false,
                writable: true,
                configurable: true
            }
        });
        if (superClass) Object.setPrototypeOf ? Object.setPrototypeOf(subClass, superClass) : subClass.__proto__ = superClass;
    };


    var possibleConstructorReturn = function (self, call) {
        if (!self) {
            throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        }

        return call && (typeof call === "object" || typeof call === "function") ? call : self;
    };


    var slicedToArray = function () {
        function sliceIterator(arr, i) {
            var _arr = [];
            var _n = true;
            var _d = false;
            var _e = undefined;

            try {
                for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {
                    _arr.push(_s.value);

                    if (i && _arr.length === i) break;
                }
            } catch (err) {
                _d = true;
                _e = err;
            } finally {
                try {
                    if (!_n && _i["return"]) _i["return"]();
                } finally {
                    if (_d) throw _e;
                }
            }

            return _arr;
        }

        return function (arr, i) {
            if (Array.isArray(arr)) {
                return arr;
            } else if (Symbol.iterator in Object(arr)) {
                return sliceIterator(arr, i);
            } else {
                throw new TypeError("Invalid attempt to destructure non-iterable instance");
            }
        };
    }();


    var toConsumableArray = function (arr) {
        if (Array.isArray(arr)) {
            for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) arr2[i] = arr[i];

            return arr2;
        } else {
            return Array.from(arr);
        }
    };

    var EventEmitter = function () {
        function EventEmitter() {
            classCallCheck(this, EventEmitter);

            this._events = {};
        }

        createClass(EventEmitter, [{
            key: "on",
            value: function on(type, fn) {
                var context = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : this;

                if (!this._events[type]) {
                    this._events[type] = [];
                }

                this._events[type].push([fn, context]);
            }
        }, {
            key: "once",
            value: function once(type, fn) {
                var context = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : this;

                var fired = false;

                function magic() {
                    this.off(type, magic);

                    if (!fired) {
                        fired = true;
                        fn.apply(context, arguments);
                    }
                }

                this.on(type, magic);
            }
        }, {
            key: "off",
            value: function off(type, fn) {
                var _events = this._events[type];
                if (!_events) {
                    return;
                }

                var count = _events.length;
                while (count--) {
                    if (_events[count][0] === fn) {
                        _events[count][0] = undefined;
                    }
                }
            }
        }, {
            key: "trigger",
            value: function trigger(type) {
                var events = this._events[type];
                if (!events) {
                    return;
                }

                var len = events.length;
                var eventsCopy = [].concat(toConsumableArray(events));
                for (var i = 0; i < len; i++) {
                    var event = eventsCopy[i];

                    var _event = slicedToArray(event, 2),
                        fn = _event[0],
                        context = _event[1];

                    if (fn) {
                        fn.apply(context, [].slice.call(arguments, 1));
                    }
                }
            }
        }]);
        return EventEmitter;
    }();

    function momentum(current, start, time, lowerMargin, wrapperSize, options) {
        var distance = current - start;
        var speed = Math.abs(distance) / time;

        var deceleration = options.deceleration,
            itemHeight = options.itemHeight,
            swipeBounceTime = options.swipeBounceTime,
            bounceTime = options.bounceTime;

        var duration = options.swipeTime;
        var rate = options.wheel ? 4 : 15;

        var destination = current + speed / deceleration * (distance < 0 ? -1 : 1);

        if (options.wheel && itemHeight) {
            destination = Math.round(destination / itemHeight) * itemHeight;
        }

        if (destination < lowerMargin) {
            destination = wrapperSize ? lowerMargin - wrapperSize / rate * speed : lowerMargin;
            duration = swipeBounceTime - bounceTime;
        } else if (destination > 0) {
            destination = wrapperSize ? wrapperSize / rate * speed : 0;
            duration = swipeBounceTime - bounceTime;
        }

        return {
            destination: Math.round(destination),
            duration: duration
        };
    }

    var TOUCH_EVENT = 1;

    var BScroll$1 = function (_EventEmitter) {
        inherits(BScroll, _EventEmitter);

        function BScroll(el, options) {
            classCallCheck(this, BScroll);

            var _this = possibleConstructorReturn(this, (BScroll.__proto__ || Object.getPrototypeOf(BScroll)).call(this));

            _this.wrapper = typeof el === 'string' ? document.querySelector(el) : el;
            _this.scroller = _this.wrapper.children[0];
            // cache style for better performance
            _this.scrollerStyle = _this.scroller.style;

            _this.options = {
                startX: 0,
                startY: 0,
                scrollY: true,
                directionLockThreshold: 5,
                momentum: true,
                bounce: true,
                selectedIndex: 0,
                rotate: 25,
                wheel: false,
                snap: false,
                snapLoop: false,
                snapThreshold: 0.1,
                swipeTime: 2500,
                bounceTime: 700,
                adjustTime: 400,
                swipeBounceTime: 1200,
                deceleration: 0.001,
                momentumLimitTime: 300,
                momentumLimitDistance: 15,
                resizePolling: 60,
                preventDefault: true,
                preventDefaultException: {
                    tagName: /^(INPUT|TEXTAREA|BUTTON|SELECT)$/
                },
                HWCompositing: true,
                useTransition: true,
                useTransform: true
            };

            extend(_this.options, options);

            _this.translateZ = _this.options.HWCompositing && hasPerspective ? ' translateZ(0)' : '';

            _this.options.useTransition = _this.options.useTransition && hasTransition;
            _this.options.useTransform = _this.options.useTransform && hasTransform;

            _this.options.eventPassthrough = _this.options.eventPassthrough === true ? 'vertical' : _this.options.eventPassthrough;
            _this.options.preventDefault = !_this.options.eventPassthrough && _this.options.preventDefault;

            // If you want eventPassthrough I have to lock one of the axes
            _this.options.scrollX = _this.options.eventPassthrough === 'horizontal' ? false : _this.options.scrollX;
            _this.options.scrollY = _this.options.eventPassthrough === 'vertical' ? false : _this.options.scrollY;

            // With eventPassthrough we also need lockDirection mechanism
            _this.options.freeScroll = _this.options.freeScroll && !_this.options.eventPassthrough;
            _this.options.directionLockThreshold = _this.options.eventPassthrough ? 0 : _this.options.directionLockThreshold;

            if (_this.options.tap === true) {
                _this.options.tap = 'tap';
            }

            _this._init();

            if (_this.options.snap) {
                _this._initSnap();
            }

            _this.refresh();

            if (!_this.options.snap) {
                _this.scrollTo(_this.options.startX, _this.options.startY);
            }

            _this.enable();
            return _this;
        }

        createClass(BScroll, [{
            key: '_init',
            value: function _init() {
                this.x = 0;
                this.y = 0;
                this.directionX = 0;
                this.directionY = 0;

                this._addEvents();
            }
        }, {
            key: '_initSnap',
            value: function _initSnap() {
                var _this2 = this;

                this.currentPage = {};

                if (this.options.snapLoop) {
                    var children = this.scroller.children;
                    if (children.length > 0) {
                        prepend(children[children.length - 1].cloneNode(true), this.scroller);
                        this.scroller.appendChild(children[1].cloneNode(true));
                    }
                }

                if (typeof this.options.snap === 'string') {
                    this.options.snap = this.scroller.querySelectorAll(this.options.snap);
                }

                this.on('refresh', function () {
                    _this2.pages = [];

                    if (!_this2.wrapperWidth || !_this2.wrapperHeight || !_this2.scrollerWidth || !_this2.scrollerHeight) {
                        return;
                    }

                    var stepX = _this2.options.snapStepX || _this2.wrapperWidth;
                    var stepY = _this2.options.snapStepY || _this2.wrapperHeight;

                    var x = 0;
                    var y = void 0;
                    var cx = void 0;
                    var cy = void 0;
                    var i = 0;
                    var l = void 0;
                    var m = 0;
                    var n = void 0;
                    var el = void 0;
                    var rect = void 0;
                    if (_this2.options.snap === true) {
                        cx = Math.round(stepX / 2);
                        cy = Math.round(stepY / 2);

                        while (x > -_this2.scrollerWidth) {
                            _this2.pages[i] = [];
                            l = 0;
                            y = 0;

                            while (y > -_this2.scrollerHeight) {
                                _this2.pages[i][l] = {
                                    x: Math.max(x, _this2.maxScrollX),
                                    y: Math.max(y, _this2.maxScrollY),
                                    width: stepX,
                                    height: stepY,
                                    cx: x - cx,
                                    cy: y - cy
                                };

                                y -= stepY;
                                l++;
                            }

                            x -= stepX;
                            i++;
                        }
                    } else {
                        el = _this2.options.snap;
                        l = el.length;
                        n = -1;

                        for (; i < l; i++) {
                            rect = getRect(el[i]);
                            if (i === 0 || rect.left <= getRect(el[i - 1]).left) {
                                m = 0;
                                n++;
                            }

                            if (!_this2.pages[m]) {
                                _this2.pages[m] = [];
                            }

                            x = Math.max(-rect.left, _this2.maxScrollX);
                            y = Math.max(-rect.top, _this2.maxScrollY);
                            cx = x - Math.round(rect.width / 2);
                            cy = y - Math.round(rect.height / 2);

                            _this2.pages[m][n] = {
                                x: x,
                                y: y,
                                width: rect.width,
                                height: rect.height,
                                cx: cx,
                                cy: cy
                            };

                            if (x > _this2.maxScrollX) {
                                m++;
                            }
                        }
                    }

                    var initPage = _this2.options.snapLoop ? 1 : 0;
                    _this2.goToPage(_this2.currentPage.pageX || initPage, _this2.currentPage.pageY || 0, 0);

                    // Update snap threshold if needed
                    if (_this2.options.snapThreshold % 1 === 0) {
                        _this2.snapThresholdX = _this2.options.snapThreshold;
                        _this2.snapThresholdY = _this2.options.snapThreshold;
                    } else {
                        _this2.snapThresholdX = Math.round(_this2.pages[_this2.currentPage.pageX][_this2.currentPage.pageY].width * _this2.options.snapThreshold);
                        _this2.snapThresholdY = Math.round(_this2.pages[_this2.currentPage.pageX][_this2.currentPage.pageY].height * _this2.options.snapThreshold);
                    }
                });

                this.on('scrollEnd', function () {
                    if (_this2.options.snapLoop) {
                        if (_this2.currentPage.pageX === 0) {
                            _this2.goToPage(_this2.pages.length - 2, _this2.currentPage.pageY, 0);
                        }
                        if (_this2.currentPage.pageX === _this2.pages.length - 1) {
                            _this2.goToPage(1, _this2.currentPage.pageY, 0);
                        }
                    }
                });

                this.on('flick', function () {
                    var time = _this2.options.snapSpeed || Math.max(Math.max(Math.min(Math.abs(_this2.x - _this2.startX), 1000), Math.min(Math.abs(_this2.y - _this2.startY), 1000)), 300);

                    _this2.goToPage(_this2.currentPage.pageX + _this2.directionX, _this2.currentPage.pageY + _this2.directionY, time);
                });
            }
        }, {
            key: '_nearestSnap',
            value: function _nearestSnap(x, y) {
                if (!this.pages.length) {
                    return {x: 0, y: 0, pageX: 0, pageY: 0};
                }

                var i = 0;
                // Check if we exceeded the snap threshold
                if (Math.abs(x - this.absStartX) <= this.snapThresholdX && Math.abs(y - this.absStartY) <= this.snapThresholdY) {
                    return this.currentPage;
                }

                if (x > 0) {
                    x = 0;
                } else if (x < this.maxScrollX) {
                    x = this.maxScrollX;
                }

                if (y > 0) {
                    y = 0;
                } else if (y < this.maxScrollY) {
                    y = this.maxScrollY;
                }

                var l = this.pages.length;
                for (; i < l; i++) {
                    if (x >= this.pages[i][0].cx) {
                        x = this.pages[i][0].x;
                        break;
                    }
                }

                l = this.pages[i].length;

                var m = 0;
                for (; m < l; m++) {
                    if (y >= this.pages[0][m].cy) {
                        y = this.pages[0][m].y;
                        break;
                    }
                }

                if (i === this.currentPage.pageX) {
                    i += this.directionX;

                    if (i < 0) {
                        i = 0;
                    } else if (i >= this.pages.length) {
                        i = this.pages.length - 1;
                    }

                    x = this.pages[i][0].x;
                }

                if (m === this.currentPage.pageY) {
                    m += this.directionY;

                    if (m < 0) {
                        m = 0;
                    } else if (m >= this.pages[0].length) {
                        m = this.pages[0].length - 1;
                    }

                    y = this.pages[0][m].y;
                }

                return {
                    x: x,
                    y: y,
                    pageX: i,
                    pageY: m
                };
            }
        }, {
            key: '_addEvents',
            value: function _addEvents() {
                var eventOperation = addEvent;
                this._handleEvents(eventOperation);
            }
        }, {
            key: '_removeEvents',
            value: function _removeEvents() {
                var eventOperation = removeEvent;
                this._handleEvents(eventOperation);
            }
        }, {
            key: '_handleEvents',
            value: function _handleEvents(eventOperation) {
                var target = this.options.bindToWrapper ? this.wrapper : window;
                eventOperation(window, 'orientationchange', this);
                eventOperation(window, 'resize', this);

                if (this.options.click) {
                    eventOperation(this.wrapper, 'click', this);
                }

                if (!this.options.disableMouse) {
                    eventOperation(this.wrapper, 'mousedown', this);
                    eventOperation(target, 'mousemove', this);
                    eventOperation(target, 'mousecancel', this);
                    eventOperation(target, 'mouseup', this);
                }

                if (hasTouch && !this.options.disableTouch) {
                    eventOperation(this.wrapper, 'touchstart', this);
                    eventOperation(target, 'touchmove', this);
                    eventOperation(target, 'touchcancel', this);
                    eventOperation(target, 'touchend', this);
                }

                eventOperation(this.scroller, style.transitionEnd, this);
            }
        }, {
            key: '_start',
            value: function _start(e) {
                var _eventType = eventType[e.type];
                if (_eventType !== TOUCH_EVENT) {
                    if (e.button !== 0) {
                        return;
                    }
                }
                if (!this.enabled || this.destroyed || this.initiated && this.initiated !== _eventType) {
                    return;
                }
                this.initiated = _eventType;

                if (this.options.preventDefault && !isBadAndroid && !preventDefaultException(e.target, this.options.preventDefaultException)) {
                    e.preventDefault();
                }

                this.moved = false;
                this.distX = 0;
                this.distY = 0;
                this.directionX = 0;
                this.directionY = 0;
                this.directionLocked = 0;

                this._transitionTime();
                this.startTime = +new Date();

                if (this.options.wheel) {
                    this.target = e.target;
                }

                if (this.options.useTransition && this.isInTransition) {
                    this.isInTransition = false;
                    var pos = this.getComputedPosition();
                    this._translate(pos.x, pos.y);
                    if (this.options.wheel) {
                        this.target = this.items[Math.round(-pos.y / this.itemHeight)];
                    } else {
                        this.trigger('scrollEnd', {
                            x: this.x,
                            y: this.y
                        });
                    }
                }

                var point = e.touches ? e.touches[0] : e;

                this.startX = this.x;
                this.startY = this.y;
                this.absStartX = this.x;
                this.absStartY = this.y;
                this.pointX = point.pageX;
                this.pointY = point.pageY;

                this.trigger('beforeScrollStart');
            }
        }, {
            key: '_move',
            value: function _move(e) {
                if (!this.enabled || this.destroyed || eventType[e.type] !== this.initiated) {
                    return;
                }

                if (this.options.preventDefault) {
                    e.preventDefault();
                }

                var point = e.touches ? e.touches[0] : e;
                var deltaX = point.pageX - this.pointX;
                var deltaY = point.pageY - this.pointY;

                this.pointX = point.pageX;
                this.pointY = point.pageY;

                this.distX += deltaX;
                this.distY += deltaY;

                var absDistX = Math.abs(this.distX);
                var absDistY = Math.abs(this.distY);

                var timestamp = +new Date();

                // We need to move at least 15 pixels for the scrolling to initiate
                if (timestamp - this.endTime > this.options.momentumLimitTime && absDistY < this.options.momentumLimitDistance && absDistX < this.options.momentumLimitDistance) {
                    return;
                }

                // If you are scrolling in one direction lock the other
                if (!this.directionLocked && !this.options.freeScroll) {
                    if (absDistX > absDistY + this.options.directionLockThreshold) {
                        this.directionLocked = 'h'; // lock horizontally
                    } else if (absDistY >= absDistX + this.options.directionLockThreshold) {
                        this.directionLocked = 'v'; // lock vertically
                    } else {
                        this.directionLocked = 'n'; // no lock
                    }
                }

                if (this.directionLocked === 'h') {
                    if (this.options.eventPassthrough === 'vertical') {
                        e.preventDefault();
                    } else if (this.options.eventPassthrough === 'horizontal') {
                        this.initiated = false;
                        return;
                    }
                    deltaY = 0;
                } else if (this.directionLocked === 'v') {
                    if (this.options.eventPassthrough === 'horizontal') {
                        e.preventDefault();
                    } else if (this.options.eventPassthrough === 'vertical') {
                        this.initiated = false;
                        return;
                    }
                    deltaX = 0;
                }

                deltaX = this.hasHorizontalScroll ? deltaX : 0;
                deltaY = this.hasVerticalScroll ? deltaY : 0;

                var newX = this.x + deltaX;
                var newY = this.y + deltaY;

                // Slow down or stop if outside of the boundaries
                if (newX > 0 || newX < this.maxScrollX) {
                    if (this.options.bounce) {
                        newX = this.x + deltaX / 3;
                    } else {
                        newX = newX > 0 ? 0 : this.maxScrollX;
                    }
                }
                if (newY > 0 || newY < this.maxScrollY) {
                    if (this.options.bounce) {
                        newY = this.y + deltaY / 3;
                    } else {
                        newY = newY > 0 ? 0 : this.maxScrollY;
                    }
                }

                // this.directionX = deltaX > 0 ? -1 : deltaX < 0 ? 1 : 0;
                // this.directionY = deltaY > 0 ? -1 : deltaY < 0 ? 1 : 0;

                if (!this.moved) {
                    this.moved = true;
                    this.trigger('scrollStart');
                }

                this._translate(newX, newY);

                if (timestamp - this.startTime > this.options.momentumLimitTime) {
                    this.startTime = timestamp;
                    this.startX = this.x;
                    this.startY = this.y;

                    if (this.options.probeType === 1) {
                        this.trigger('scroll', {
                            x: this.x,
                            y: this.y
                        });
                    }
                }

                if (this.options.probeType > 1) {
                    this.trigger('scroll', {
                        x: this.x,
                        y: this.y
                    });
                }

                var scrollLeft = document.documentElement.scrollLeft || window.pageXOffset || document.body.scrollLeft;
                var scrollTop = document.documentElement.scrollTop || window.pageYOffset || document.body.scrollTop;

                var pX = this.pointX - scrollLeft;
                var pY = this.pointY - scrollTop;

                if (pX > document.documentElement.clientWidth - this.options.momentumLimitDistance || pX < this.options.momentumLimitDistance || pY < this.options.momentumLimitDistance || pY > document.documentElement.clientHeight - this.options.momentumLimitDistance) {
                    this._end(e);
                }
            }
        }, {
            key: '_end',
            value: function _end(e) {
                if (!this.enabled || this.destroyed || eventType[e.type] !== this.initiated) {
                    return;
                }
                this.initiated = false;

                if (this.options.preventDefault && !preventDefaultException(e.target, this.options.preventDefaultException)) {
                    e.preventDefault();
                }

                this.trigger('touchend', {
                    x: this.x,
                    y: this.y
                });

                // reset if we are outside of the boundaries
                if (this.resetPosition(this.options.bounceTime, ease.bounce)) {
                    return;
                }
                this.isInTransition = false;
                // ensures that the last position is rounded
                var newX = Math.round(this.x);
                var newY = Math.round(this.y);

                // we scrolled less than 15 pixels
                if (!this.moved) {
                    if (this.options.wheel) {
                        if (this.target && this.target.className === 'wheel-scroll') {
                            var index = Math.abs(Math.round(newY / this.itemHeight));
                            var _offset = Math.round((this.pointY + offset(this.target).top - this.itemHeight / 2) / this.itemHeight);
                            this.target = this.items[index + _offset];
                        }
                        this.scrollToElement(this.target, this.options.adjustTime, true, true, ease.swipe);
                    } else {
                        if (this.options.tap) {
                            tap(e, this.options.tap);
                        }

                        if (this.options.click) {
                            click(e);
                        }
                    }
                    this.trigger('scrollCancel');
                    return;
                }

                this.scrollTo(newX, newY);

                var deltaX = newX - this.absStartX;
                var deltaY = newY - this.absStartY;
                this.directionX = deltaX > 0 ? -1 : deltaX < 0 ? 1 : 0;
                this.directionY = deltaY > 0 ? -1 : deltaY < 0 ? 1 : 0;

                this.endTime = +new Date();

                var duration = this.endTime - this.startTime;
                var absDistX = Math.abs(newX - this.startX);
                var absDistY = Math.abs(newY - this.startY);

                // fastclick
                if (this._events.flick && duration < this.options.momentumLimitTime && absDistX < this.options.momentumLimitDistance && absDistY < this.options.momentumLimitDistance) {
                    this.trigger('flick');
                    return;
                }

                var time = 0;
                // start momentum animation if needed
                if (this.options.momentum && duration < this.options.momentumLimitTime && (absDistY > this.options.momentumLimitDistance || absDistX > this.options.momentumLimitDistance)) {
                    var momentumX = this.hasHorizontalScroll ? momentum(this.x, this.startX, duration, this.maxScrollX, this.options.bounce ? this.wrapperWidth : 0, this.options) : {
                        destination: newX,
                        duration: 0
                    };
                    var momentumY = this.hasVerticalScroll ? momentum(this.y, this.startY, duration, this.maxScrollY, this.options.bounce ? this.wrapperHeight : 0, this.options) : {
                        destination: newY,
                        duration: 0
                    };
                    newX = momentumX.destination;
                    newY = momentumY.destination;
                    time = Math.max(momentumX.duration, momentumY.duration);
                    this.isInTransition = 1;
                } else {
                    if (this.options.wheel) {
                        newY = Math.round(newY / this.itemHeight) * this.itemHeight;
                        time = this.options.adjustTime;
                    }
                }

                var easing = ease.swipe;
                if (this.options.snap) {
                    var snap = this._nearestSnap(newX, newY);
                    this.currentPage = snap;
                    time = this.options.snapSpeed || Math.max(Math.max(Math.min(Math.abs(newX - snap.x), 1000), Math.min(Math.abs(newY - snap.y), 1000)), 300);
                    newX = snap.x;
                    newY = snap.y;

                    this.directionX = 0;
                    this.directionY = 0;
                    easing = ease.bounce;
                }

                if (newX !== this.x || newY !== this.y) {
                    // change easing function when scroller goes out of the boundaries
                    if (newX > 0 || newX < this.maxScrollX || newY > 0 || newY < this.maxScrollY) {
                        easing = ease.swipeBounce;
                    }
                    this.scrollTo(newX, newY, time, easing);
                    return;
                }

                if (this.options.wheel) {
                    this.selectedIndex = Math.abs(this.y / this.itemHeight) | 0;
                }
                this.trigger('scrollEnd', {
                    x: this.x,
                    y: this.y
                });
            }
        }, {
            key: '_resize',
            value: function _resize() {
                var _this3 = this;

                if (!this.enabled) {
                    return;
                }

                clearTimeout(this.resizeTimeout);
                this.resizeTimeout = setTimeout(function () {
                    _this3.refresh();
                }, this.options.resizePolling);
            }
        }, {
            key: '_startProbe',
            value: function _startProbe() {
                cancelAnimationFrame(this.probeTimer);
                this.probeTimer = requestAnimationFrame(probe);

                var me = this;

                function probe() {
                    var pos = me.getComputedPosition();
                    me.trigger('scroll', pos);
                    if (me.isInTransition) {
                        me.probeTimer = requestAnimationFrame(probe);
                    }
                }
            }
        }, {
            key: '_transitionTime',
            value: function _transitionTime() {
                var _this4 = this;

                var time = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;

                this.scrollerStyle[style.transitionDuration] = time + 'ms';

                if (this.options.wheel && !isBadAndroid) {
                    for (var i = 0; i < this.items.length; i++) {
                        this.items[i].style[style.transitionDuration] = time + 'ms';
                    }
                }

                if (!time && isBadAndroid) {
                    this.scrollerStyle[style.transitionDuration] = '0.001s';

                    requestAnimationFrame(function () {
                        if (_this4.scrollerStyle[style.transitionDuration] === '0.0001ms') {
                            _this4.scrollerStyle[style.transitionDuration] = '0s';
                        }
                    });
                }
            }
        }, {
            key: '_transitionTimingFunction',
            value: function _transitionTimingFunction(easing) {
                this.scrollerStyle[style.transitionTimingFunction] = easing;

                if (this.options.wheel && !isBadAndroid) {
                    for (var i = 0; i < this.items.length; i++) {
                        this.items[i].style[style.transitionTimingFunction] = easing;
                    }
                }
            }
        }, {
            key: '_transitionEnd',
            value: function _transitionEnd(e) {
                if (e.target !== this.scroller || !this.isInTransition) {
                    return;
                }

                this._transitionTime();
                if (!this.resetPosition(this.options.bounceTime, ease.bounce)) {
                    this.isInTransition = false;
                    this.trigger('scrollEnd', {
                        x: this.x,
                        y: this.y
                    });
                }
            }
        }, {
            key: '_translate',
            value: function _translate(x, y) {
                if (this.options.useTransform) {
                    this.scrollerStyle[style.transform] = 'translate(' + x + 'px,' + y + 'px)' + this.translateZ;
                } else {
                    x = Math.round(x);
                    y = Math.round(y);
                    this.scrollerStyle.left = x + 'px';
                    this.scrollerStyle.top = y + 'px';
                }

                if (this.options.wheel && !isBadAndroid) {
                    for (var i = 0; i < this.items.length; i++) {
                        var deg = this.options.rotate * (y / this.itemHeight + i);
                        this.items[i].style[style.transform] = 'rotateX(' + deg + 'deg)';
                    }
                }

                this.x = x;
                this.y = y;
            }
        }, {
            key: 'enable',
            value: function enable() {
                this.enabled = true;
            }
        }, {
            key: 'disable',
            value: function disable() {
                this.enabled = false;
            }
        }, {
            key: 'refresh',
            value: function refresh() {
                /* eslint-disable no-unused-vars */
                var rf = this.wrapper.offsetHeight;

                this.wrapperWidth = parseInt(this.wrapper.style.width) || this.wrapper.clientWidth;
                this.wrapperHeight = parseInt(this.wrapper.style.height) || this.wrapper.clientHeight;

                this.scrollerWidth = parseInt(this.scroller.style.width) || this.scroller.clientWidth;
                this.scrollerHeight = parseInt(this.scroller.style.height) || this.scroller.clientHeight;
                if (this.options.wheel) {
                    this.items = this.scroller.children;
                    this.options.itemHeight = this.itemHeight = this.items.length ? this.items[0].clientHeight : 0;
                    if (this.selectedIndex === undefined) {
                        this.selectedIndex = this.options.selectedIndex;
                    }
                    this.options.startY = -this.selectedIndex * this.itemHeight;
                    this.maxScrollX = 0;
                    this.maxScrollY = -this.itemHeight * (this.items.length - 1);
                } else {
                    this.maxScrollX = this.wrapperWidth - this.scrollerWidth;
                    this.maxScrollY = this.wrapperHeight - this.scrollerHeight;
                }

                this.hasHorizontalScroll = this.options.scrollX && this.maxScrollX < 0;
                this.hasVerticalScroll = this.options.scrollY && this.maxScrollY < 0;

                if (!this.hasHorizontalScroll) {
                    this.maxScrollX = 0;
                    this.scrollerWidth = this.wrapperWidth;
                }

                if (!this.hasVerticalScroll) {
                    this.maxScrollY = 0;
                    this.scrollerHeight = this.wrapperHeight;
                }

                this.endTime = 0;
                this.directionX = 0;
                this.directionY = 0;
                this.wrapperOffset = offset(this.wrapper);

                this.trigger('refresh');

                this.resetPosition();
            }
        }, {
            key: 'resetPosition',
            value: function resetPosition() {
                var time = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 0;
                var easeing = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : ease.bounce;

                var x = this.x;
                if (!this.hasHorizontalScroll || x > 0) {
                    x = 0;
                } else if (x < this.maxScrollX) {
                    x = this.maxScrollX;
                }

                var y = this.y;
                if (!this.hasVerticalScroll || y > 0) {
                    y = 0;
                } else if (y < this.maxScrollY) {
                    y = this.maxScrollY;
                }

                if (x === this.x && y === this.y) {
                    return false;
                }

                this.scrollTo(x, y, time, easeing);

                return true;
            }
        }, {
            key: 'wheelTo',
            value: function wheelTo(selectIndex) {
                if (this.options.wheel) {
                    this.y = -selectIndex * this.itemHeight;
                    this.scrollTo(0, this.y);
                }
            }
        }, {
            key: 'scrollBy',
            value: function scrollBy(x, y) {
                var time = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 0;
                var easing = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : ease.bounce;

                x = this.x + x;
                y = this.y + y;

                this.scrollTo(x, y, time, easing);
            }
        }, {
            key: 'scrollTo',
            value: function scrollTo(x, y, time) {
                var easing = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : ease.bounce;

                this.isInTransition = this.options.useTransition && time > 0 && (x !== this.x || y !== this.y);

                if (!time || this.options.useTransition) {
                    this._transitionTimingFunction(easing.style);
                    this._transitionTime(time);
                    this._translate(x, y);

                    if (time && this.options.probeType === 3) {
                        this._startProbe();
                    }

                    if (this.options.wheel) {
                        if (y > 0) {
                            this.selectedIndex = 0;
                        } else if (y < this.maxScrollY) {
                            this.selectedIndex = this.items.length - 1;
                        } else {
                            this.selectedIndex = Math.abs(y / this.itemHeight) | 0;
                        }
                    }
                }
            }
        }, {
            key: 'getSelectedIndex',
            value: function getSelectedIndex() {
                return this.options.wheel && this.selectedIndex;
            }
        }, {
            key: 'getCurrentPage',
            value: function getCurrentPage() {
                return this.options.snap && this.currentPage;
            }
        }, {
            key: 'scrollToElement',
            value: function scrollToElement(el, time, offsetX, offsetY, easing) {
                if (!el) {
                    return;
                }
                el = el.nodeType ? el : this.scroller.querySelector(el);

                if (this.options.wheel && el.className !== 'wheel-item') {
                    return;
                }

                var pos = offset(el);
                pos.left -= this.wrapperOffset.left;
                pos.top -= this.wrapperOffset.top;

                // if offsetX/Y are true we center the element to the screen
                if (offsetX === true) {
                    offsetX = Math.round(el.offsetWidth / 2 - this.wrapper.offsetWidth / 2);
                }
                if (offsetY === true) {
                    offsetY = Math.round(el.offsetHeight / 2 - this.wrapper.offsetHeight / 2);
                }

                pos.left -= offsetX || 0;
                pos.top -= offsetY || 0;
                pos.left = pos.left > 0 ? 0 : pos.left < this.maxScrollX ? this.maxScrollX : pos.left;
                pos.top = pos.top > 0 ? 0 : pos.top < this.maxScrollY ? this.maxScrollY : pos.top;

                if (this.options.wheel) {
                    pos.top = Math.round(pos.top / this.itemHeight) * this.itemHeight;
                }

                time = time === undefined || time === null || time === 'auto' ? Math.max(Math.abs(this.x - pos.left), Math.abs(this.y - pos.top)) : time;

                this.scrollTo(pos.left, pos.top, time, easing);
            }
        }, {
            key: 'getComputedPosition',
            value: function getComputedPosition() {
                var matrix = window.getComputedStyle(this.scroller, null);
                var x = void 0;
                var y = void 0;

                if (this.options.useTransform) {
                    matrix = matrix[style.transform].split(')')[0].split(', ');
                    x = +(matrix[12] || matrix[4]);
                    y = +(matrix[13] || matrix[5]);
                } else {
                    x = +matrix.left.replace(/[^-\d.]/g, '');
                    y = +matrix.top.replace(/[^-\d.]/g, '');
                }

                return {
                    x: x,
                    y: y
                };
            }
        }, {
            key: 'goToPage',
            value: function goToPage(x, y, time) {
                var easing = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : ease.bounce;

                if (x >= this.pages.length) {
                    x = this.pages.length - 1;
                } else if (x < 0) {
                    x = 0;
                }

                if (y >= this.pages[x].length) {
                    y = this.pages[x].length - 1;
                } else if (y < 0) {
                    y = 0;
                }

                var posX = this.pages[x][y].x;
                var posY = this.pages[x][y].y;

                time = time === undefined ? this.options.snapSpeed || Math.max(Math.max(Math.min(Math.abs(posX - this.x), 1000), Math.min(Math.abs(posY - this.y), 1000)), 300) : time;

                this.currentPage = {
                    x: posX,
                    y: posY,
                    pageX: x,
                    pageY: y
                };
                this.scrollTo(posX, posY, time, easing);
            }
        }, {
            key: 'next',
            value: function next(time, easing) {
                var x = this.currentPage.pageX;
                var y = this.currentPage.pageY;

                x++;
                if (x >= this.pages.length && this.hasVerticalScroll) {
                    x = 0;
                    y++;
                }

                this.goToPage(x, y, time, easing);
            }
        }, {
            key: 'prev',
            value: function prev(time, easing) {
                var x = this.currentPage.pageX;
                var y = this.currentPage.pageY;

                x--;
                if (x < 0 && this.hasVerticalScroll) {
                    x = 0;
                    y--;
                }

                this.goToPage(x, y, time, easing);
            }
        }, {
            key: 'destroy',
            value: function destroy() {
                this._removeEvents();

                this.destroyed = true;
                this.trigger('destroy');
            }
        }, {
            key: 'handleEvent',
            value: function handleEvent(e) {
                switch (e.type) {
                    case 'touchstart':
                    case 'mousedown':
                        this._start(e);
                        break;
                    case 'touchmove':
                    case 'mousemove':
                        this._move(e);
                        break;
                    case 'touchend':
                    case 'mouseup':
                    case 'touchcancel':
                    case 'mousecancel':
                        this._end(e);
                        break;
                    case 'orientationchange':
                    case 'resize':
                        this._resize();
                        break;
                    case 'transitionend':
                    case 'webkitTransitionEnd':
                    case 'oTransitionEnd':
                    case 'MSTransitionEnd':
                        this._transitionEnd(e);
                        break;
                    case 'click':
                        if (this.enabled && !e._constructed && !/(SELECT|INPUT|TEXTAREA)/i.test(e.target.tagName)) {
                            e.preventDefault();
                            e.stopPropagation();
                        }
                        break;
                }
            }
        }]);
        return BScroll;
    }(EventEmitter);

    BScroll$1.Version = '0.4.0';

    return BScroll$1;

})));

/*
 * $Id: base64.js,v 2.15 2014/04/05 12:58:57 dankogai Exp dankogai $
 *
 *  Licensed under the BSD 3-Clause License.
 *    http://opensource.org/licenses/BSD-3-Clause
 *
 *  References:
 *    http://en.wikipedia.org/wiki/Base64
 */

(function (global) {
    'use strict';
    // existing version for noConflict()
    var _Base64 = global.Base64;
    var version = "2.1.9";
    // if node.js, we use Buffer
    var buffer;
    if (typeof module !== 'undefined' && module.exports) {
        try {
            buffer = require('buffer').Buffer;
        } catch (err) {
        }
    }
    // constants
    var b64chars
        = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
    var b64tab = function (bin) {
        var t = {};
        for (var i = 0, l = bin.length; i < l; i++) t[bin.charAt(i)] = i;
        return t;
    }(b64chars);
    var fromCharCode = String.fromCharCode;
    // encoder stuff
    var cb_utob = function (c) {
        if (c.length < 2) {
            var cc = c.charCodeAt(0);
            return cc < 0x80 ? c
                : cc < 0x800 ? (fromCharCode(0xc0 | (cc >>> 6))
                    + fromCharCode(0x80 | (cc & 0x3f)))
                    : (fromCharCode(0xe0 | ((cc >>> 12) & 0x0f))
                        + fromCharCode(0x80 | ((cc >>> 6) & 0x3f))
                        + fromCharCode(0x80 | (cc & 0x3f)));
        } else {
            var cc = 0x10000
                + (c.charCodeAt(0) - 0xD800) * 0x400
                + (c.charCodeAt(1) - 0xDC00);
            return (fromCharCode(0xf0 | ((cc >>> 18) & 0x07))
                + fromCharCode(0x80 | ((cc >>> 12) & 0x3f))
                + fromCharCode(0x80 | ((cc >>> 6) & 0x3f))
                + fromCharCode(0x80 | (cc & 0x3f)));
        }
    };
    var re_utob = /[\uD800-\uDBFF][\uDC00-\uDFFFF]|[^\x00-\x7F]/g;
    var utob = function (u) {
        return u.replace(re_utob, cb_utob);
    };
    var cb_encode = function (ccc) {
        var padlen = [0, 2, 1][ccc.length % 3],
            ord = ccc.charCodeAt(0) << 16
                | ((ccc.length > 1 ? ccc.charCodeAt(1) : 0) << 8)
                | ((ccc.length > 2 ? ccc.charCodeAt(2) : 0)),
            chars = [
                b64chars.charAt(ord >>> 18),
                b64chars.charAt((ord >>> 12) & 63),
                padlen >= 2 ? '=' : b64chars.charAt((ord >>> 6) & 63),
                padlen >= 1 ? '=' : b64chars.charAt(ord & 63)
            ];
        return chars.join('');
    };
    var btoa = global.btoa ? function (b) {
        return global.btoa(b);
    } : function (b) {
        return b.replace(/[\s\S]{1,3}/g, cb_encode);
    };
    var _encode = buffer ? function (u) {
            return (u.constructor === buffer.constructor ? u : new buffer(u))
                .toString('base64')
        }
        : function (u) {
            return btoa(utob(u))
        }
    ;
    var encode = function (u, urisafe) {
        return !urisafe
            ? _encode(String(u))
            : _encode(String(u)).replace(/[+\/]/g, function (m0) {
                return m0 == '+' ? '-' : '_';
            }).replace(/=/g, '');
    };
    var encodeURI = function (u) {
        return encode(u, true)
    };
    // decoder stuff
    var re_btou = new RegExp([
        '[\xC0-\xDF][\x80-\xBF]',
        '[\xE0-\xEF][\x80-\xBF]{2}',
        '[\xF0-\xF7][\x80-\xBF]{3}'
    ].join('|'), 'g');
    var cb_btou = function (cccc) {
        switch (cccc.length) {
            case 4:
                var cp = ((0x07 & cccc.charCodeAt(0)) << 18)
                    | ((0x3f & cccc.charCodeAt(1)) << 12)
                    | ((0x3f & cccc.charCodeAt(2)) << 6)
                    | (0x3f & cccc.charCodeAt(3)),
                    offset = cp - 0x10000;
                return (fromCharCode((offset >>> 10) + 0xD800)
                    + fromCharCode((offset & 0x3FF) + 0xDC00));
            case 3:
                return fromCharCode(
                    ((0x0f & cccc.charCodeAt(0)) << 12)
                    | ((0x3f & cccc.charCodeAt(1)) << 6)
                    | (0x3f & cccc.charCodeAt(2))
                );
            default:
                return fromCharCode(
                    ((0x1f & cccc.charCodeAt(0)) << 6)
                    | (0x3f & cccc.charCodeAt(1))
                );
        }
    };
    var btou = function (b) {
        return b.replace(re_btou, cb_btou);
    };
    var cb_decode = function (cccc) {
        var len = cccc.length,
            padlen = len % 4,
            n = (len > 0 ? b64tab[cccc.charAt(0)] << 18 : 0)
                | (len > 1 ? b64tab[cccc.charAt(1)] << 12 : 0)
                | (len > 2 ? b64tab[cccc.charAt(2)] << 6 : 0)
                | (len > 3 ? b64tab[cccc.charAt(3)] : 0),
            chars = [
                fromCharCode(n >>> 16),
                fromCharCode((n >>> 8) & 0xff),
                fromCharCode(n & 0xff)
            ];
        chars.length -= [0, 0, 2, 1][padlen];
        return chars.join('');
    };
    var atob = global.atob ? function (a) {
        return global.atob(a);
    } : function (a) {
        return a.replace(/[\s\S]{1,4}/g, cb_decode);
    };
    var _decode = buffer ? function (a) {
            return (a.constructor === buffer.constructor
                ? a : new buffer(a, 'base64')).toString();
        }
        : function (a) {
            return btou(atob(a))
        };
    var decode = function (a) {
        return _decode(
            String(a).replace(/[-_]/g, function (m0) {
                return m0 == '-' ? '+' : '/'
            })
                .replace(/[^A-Za-z0-9\+\/]/g, '')
        );
    };
    var noConflict = function () {
        var Base64 = global.Base64;
        global.Base64 = _Base64;
        return Base64;
    };
    // export Base64
    global.Base64 = {
        VERSION: version,
        atob: atob,
        btoa: btoa,
        fromBase64: decode,
        toBase64: encode,
        utob: utob,
        encode: encode,
        encodeURI: encodeURI,
        btou: btou,
        decode: decode,
        noConflict: noConflict
    };
    // if ES5 is available, make Base64.extendString() available
    if (typeof Object.defineProperty === 'function') {
        var noEnum = function (v) {
            return {value: v, enumerable: false, writable: true, configurable: true};
        };
        global.Base64.extendString = function () {
            Object.defineProperty(
                String.prototype, 'fromBase64', noEnum(function () {
                    return decode(this)
                }));
            Object.defineProperty(
                String.prototype, 'toBase64', noEnum(function (urisafe) {
                    return encode(this, urisafe)
                }));
            Object.defineProperty(
                String.prototype, 'toBase64URI', noEnum(function () {
                    return encode(this, true)
                }));
        };
    }
    // that's it!
    if (global['Meteor']) {
        Base64 = global.Base64; // for normal export in Meteor.js
    }
})(this);

/*!
 * Vue.js v2.3.3
 * (c) 2014-2017 Evan You
 * Released under the MIT License.
 */
!function (e, t) {
    "object" == typeof exports && "undefined" != typeof module ? module.exports = t() : "function" == typeof define && define.amd ? define(t) : e.Vue = t()
}(this, function () {
    "use strict";

    function e(e) {
        return void 0 === e || null === e
    }

    function t(e) {
        return void 0 !== e && null !== e
    }

    function n(e) {
        return !0 === e
    }

    function r(e) {
        return !1 === e
    }

    function i(e) {
        return "string" == typeof e || "number" == typeof e
    }

    function o(e) {
        return null !== e && "object" == typeof e
    }

    function a(e) {
        return "[object Object]" === Ti.call(e)
    }

    function s(e) {
        return "[object RegExp]" === Ti.call(e)
    }

    function c(e) {
        return null == e ? "" : "object" == typeof e ? JSON.stringify(e, null, 2) : String(e)
    }

    function u(e) {
        var t = parseFloat(e);
        return isNaN(t) ? e : t
    }

    function l(e, t) {
        for (var n = Object.create(null), r = e.split(","), i = 0; i < r.length; i++) n[r[i]] = !0;
        return t ? function (e) {
            return n[e.toLowerCase()]
        } : function (e) {
            return n[e]
        }
    }

    function f(e, t) {
        if (e.length) {
            var n = e.indexOf(t);
            if (n > -1) return e.splice(n, 1)
        }
    }

    function p(e, t) {
        return ji.call(e, t)
    }

    function d(e) {
        var t = Object.create(null);
        return function (n) {
            return t[n] || (t[n] = e(n))
        }
    }

    function v(e, t) {
        function n(n) {
            var r = arguments.length;
            return r ? r > 1 ? e.apply(t, arguments) : e.call(t, n) : e.call(t)
        }

        return n._length = e.length, n
    }

    function h(e, t) {
        t = t || 0;
        for (var n = e.length - t, r = new Array(n); n--;) r[n] = e[n + t];
        return r
    }

    function m(e, t) {
        for (var n in t) e[n] = t[n];
        return e
    }

    function g(e) {
        for (var t = {}, n = 0; n < e.length; n++) e[n] && m(t, e[n]);
        return t
    }

    function y() {
    }

    function _(e, t) {
        var n = o(e), r = o(t);
        if (!n || !r) return !n && !r && String(e) === String(t);
        try {
            return JSON.stringify(e) === JSON.stringify(t)
        } catch (n) {
            return e === t
        }
    }

    function b(e, t) {
        for (var n = 0; n < e.length; n++) if (_(e[n], t)) return n;
        return -1
    }

    function $(e) {
        var t = !1;
        return function () {
            t || (t = !0, e.apply(this, arguments))
        }
    }

    function C(e) {
        var t = (e + "").charCodeAt(0);
        return 36 === t || 95 === t
    }

    function x(e, t, n, r) {
        Object.defineProperty(e, t, {value: n, enumerable: !!r, writable: !0, configurable: !0})
    }

    function w(e) {
        if (!Ui.test(e)) {
            var t = e.split(".");
            return function (e) {
                for (var n = 0; n < t.length; n++) {
                    if (!e) return;
                    e = e[t[n]]
                }
                return e
            }
        }
    }

    function k(e, t, n) {
        if (Bi.errorHandler) Bi.errorHandler.call(null, e, t, n); else {
            if (!Ji || "undefined" == typeof console) throw e;
            console.error(e)
        }
    }

    function A(e) {
        return "function" == typeof e && /native code/.test(e.toString())
    }

    function O(e) {
        co.target && uo.push(co.target), co.target = e
    }

    function S() {
        co.target = uo.pop()
    }

    function T(e, t) {
        e.__proto__ = t
    }

    function E(e, t, n) {
        for (var r = 0, i = n.length; r < i; r++) {
            var o = n[r];
            x(e, o, t[o])
        }
    }

    function j(e, t) {
        if (o(e)) {
            var n;
            return p(e, "__ob__") && e.__ob__ instanceof ho ? n = e.__ob__ : vo.shouldConvert && !ro() && (Array.isArray(e) || a(e)) && Object.isExtensible(e) && !e._isVue && (n = new ho(e)), t && n && n.vmCount++, n
        }
    }

    function N(e, t, n, r) {
        var i = new co, o = Object.getOwnPropertyDescriptor(e, t);
        if (!o || !1 !== o.configurable) {
            var a = o && o.get, s = o && o.set, c = j(n);
            Object.defineProperty(e, t, {
                enumerable: !0, configurable: !0, get: function () {
                    var t = a ? a.call(e) : n;
                    return co.target && (i.depend(), c && c.dep.depend(), Array.isArray(t) && D(t)), t
                }, set: function (t) {
                    var r = a ? a.call(e) : n;
                    t === r || t !== t && r !== r || (s ? s.call(e, t) : n = t, c = j(t), i.notify())
                }
            })
        }
    }

    function L(e, t, n) {
        if (Array.isArray(e) && "number" == typeof t) return e.length = Math.max(e.length, t), e.splice(t, 1, n), n;
        if (p(e, t)) return e[t] = n, n;
        var r = e.__ob__;
        return e._isVue || r && r.vmCount ? n : r ? (N(r.value, t, n), r.dep.notify(), n) : (e[t] = n, n)
    }

    function I(e, t) {
        if (Array.isArray(e) && "number" == typeof t) return void e.splice(t, 1);
        var n = e.__ob__;
        e._isVue || n && n.vmCount || p(e, t) && (delete e[t], n && n.dep.notify())
    }

    function D(e) {
        for (var t = void 0, n = 0, r = e.length; n < r; n++) t = e[n], t && t.__ob__ && t.__ob__.dep.depend(), Array.isArray(t) && D(t)
    }

    function M(e, t) {
        if (!t) return e;
        for (var n, r, i, o = Object.keys(t), s = 0; s < o.length; s++) n = o[s], r = e[n], i = t[n], p(e, n) ? a(r) && a(i) && M(r, i) : L(e, n, i);
        return e
    }

    function P(e, t) {
        return t ? e ? e.concat(t) : Array.isArray(t) ? t : [t] : e
    }

    function R(e, t) {
        var n = Object.create(e || null);
        return t ? m(n, t) : n
    }

    function F(e) {
        var t = e.props;
        if (t) {
            var n, r, i, o = {};
            if (Array.isArray(t)) for (n = t.length; n--;) "string" == typeof(r = t[n]) && (i = Ni(r), o[i] = {type: null}); else if (a(t)) for (var s in t) r = t[s], i = Ni(s), o[i] = a(r) ? r : {type: r};
            e.props = o
        }
    }

    function B(e) {
        var t = e.directives;
        if (t) for (var n in t) {
            var r = t[n];
            "function" == typeof r && (t[n] = {bind: r, update: r})
        }
    }

    function H(e, t, n) {
        function r(r) {
            var i = mo[r] || go;
            c[r] = i(e[r], t[r], n, r)
        }

        "function" == typeof t && (t = t.options), F(t), B(t);
        var i = t.extends;
        if (i && (e = H(e, i, n)), t.mixins) for (var o = 0, a = t.mixins.length; o < a; o++) e = H(e, t.mixins[o], n);
        var s, c = {};
        for (s in e) r(s);
        for (s in t) p(e, s) || r(s);
        return c
    }

    function U(e, t, n, r) {
        if ("string" == typeof n) {
            var i = e[t];
            if (p(i, n)) return i[n];
            var o = Ni(n);
            if (p(i, o)) return i[o];
            var a = Li(o);
            if (p(i, a)) return i[a];
            var s = i[n] || i[o] || i[a];
            return s
        }
    }

    function V(e, t, n, r) {
        var i = t[e], o = !p(n, e), a = n[e];
        if (K(Boolean, i.type) && (o && !p(i, "default") ? a = !1 : K(String, i.type) || "" !== a && a !== Ii(e) || (a = !0)), void 0 === a) {
            a = z(r, i, e);
            var s = vo.shouldConvert;
            vo.shouldConvert = !0, j(a), vo.shouldConvert = s
        }
        return a
    }

    function z(e, t, n) {
        if (p(t, "default")) {
            var r = t.default;
            return e && e.$options.propsData && void 0 === e.$options.propsData[n] && void 0 !== e._props[n] ? e._props[n] : "function" == typeof r && "Function" !== J(t.type) ? r.call(e) : r
        }
    }

    function J(e) {
        var t = e && e.toString().match(/^\s*function (\w+)/);
        return t ? t[1] : ""
    }

    function K(e, t) {
        if (!Array.isArray(t)) return J(t) === J(e);
        for (var n = 0, r = t.length; n < r; n++) if (J(t[n]) === J(e)) return !0;
        return !1
    }

    function q(e) {
        return new yo(void 0, void 0, void 0, String(e))
    }

    function W(e) {
        var t = new yo(e.tag, e.data, e.children, e.text, e.elm, e.context, e.componentOptions);
        return t.ns = e.ns, t.isStatic = e.isStatic, t.key = e.key, t.isComment = e.isComment, t.isCloned = !0, t
    }

    function Z(e) {
        for (var t = e.length, n = new Array(t), r = 0; r < t; r++) n[r] = W(e[r]);
        return n
    }

    function G(e) {
        function t() {
            var e = arguments, n = t.fns;
            if (!Array.isArray(n)) return n.apply(null, arguments);
            for (var r = 0; r < n.length; r++) n[r].apply(null, e)
        }

        return t.fns = e, t
    }

    function Y(t, n, r, i, o) {
        var a, s, c, u;
        for (a in t) s = t[a], c = n[a], u = Co(a), e(s) || (e(c) ? (e(s.fns) && (s = t[a] = G(s)), r(u.name, s, u.once, u.capture, u.passive)) : s !== c && (c.fns = s, t[a] = c));
        for (a in n) e(t[a]) && (u = Co(a), i(u.name, n[a], u.capture))
    }

    function Q(r, i, o) {
        function a() {
            o.apply(this, arguments), f(s.fns, a)
        }

        var s, c = r[i];
        e(c) ? s = G([a]) : t(c.fns) && n(c.merged) ? (s = c, s.fns.push(a)) : s = G([c, a]), s.merged = !0, r[i] = s
    }

    function X(n, r, i) {
        var o = r.options.props;
        if (!e(o)) {
            var a = {}, s = n.attrs, c = n.props;
            if (t(s) || t(c)) for (var u in o) {
                var l = Ii(u);
                ee(a, c, u, l, !0) || ee(a, s, u, l, !1)
            }
            return a
        }
    }

    function ee(e, n, r, i, o) {
        if (t(n)) {
            if (p(n, r)) return e[r] = n[r], o || delete n[r], !0;
            if (p(n, i)) return e[r] = n[i], o || delete n[i], !0
        }
        return !1
    }

    function te(e) {
        for (var t = 0; t < e.length; t++) if (Array.isArray(e[t])) return Array.prototype.concat.apply([], e);
        return e
    }

    function ne(e) {
        return i(e) ? [q(e)] : Array.isArray(e) ? ie(e) : void 0
    }

    function re(e) {
        return t(e) && t(e.text) && r(e.isComment)
    }

    function ie(r, o) {
        var a, s, c, u = [];
        for (a = 0; a < r.length; a++) s = r[a], e(s) || "boolean" == typeof s || (c = u[u.length - 1], Array.isArray(s) ? u.push.apply(u, ie(s, (o || "") + "_" + a)) : i(s) ? re(c) ? c.text += String(s) : "" !== s && u.push(q(s)) : re(s) && re(c) ? u[u.length - 1] = q(c.text + s.text) : (n(r._isVList) && t(s.tag) && e(s.key) && t(o) && (s.key = "__vlist" + o + "_" + a + "__"), u.push(s)));
        return u
    }

    function oe(e, t) {
        return o(e) ? t.extend(e) : e
    }

    function ae(r, i, a) {
        if (n(r.error) && t(r.errorComp)) return r.errorComp;
        if (t(r.resolved)) return r.resolved;
        if (n(r.loading) && t(r.loadingComp)) return r.loadingComp;
        if (!t(r.contexts)) {
            var s = r.contexts = [a], c = !0, u = function () {
                for (var e = 0, t = s.length; e < t; e++) s[e].$forceUpdate()
            }, l = $(function (e) {
                r.resolved = oe(e, i), c || u()
            }), f = $(function (e) {
                t(r.errorComp) && (r.error = !0, u())
            }), p = r(l, f);
            return o(p) && ("function" == typeof p.then ? e(r.resolved) && p.then(l, f) : t(p.component) && "function" == typeof p.component.then && (p.component.then(l, f), t(p.error) && (r.errorComp = oe(p.error, i)), t(p.loading) && (r.loadingComp = oe(p.loading, i), 0 === p.delay ? r.loading = !0 : setTimeout(function () {
                e(r.resolved) && e(r.error) && (r.loading = !0, u())
            }, p.delay || 200)), t(p.timeout) && setTimeout(function () {
                e(r.resolved) && f(null)
            }, p.timeout))), c = !1, r.loading ? r.loadingComp : r.resolved
        }
        r.contexts.push(a)
    }

    function se(e) {
        if (Array.isArray(e)) for (var n = 0; n < e.length; n++) {
            var r = e[n];
            if (t(r) && t(r.componentOptions)) return r
        }
    }

    function ce(e) {
        e._events = Object.create(null), e._hasHookEvent = !1;
        var t = e.$options._parentListeners;
        t && fe(e, t)
    }

    function ue(e, t, n) {
        n ? bo.$once(e, t) : bo.$on(e, t)
    }

    function le(e, t) {
        bo.$off(e, t)
    }

    function fe(e, t, n) {
        bo = e, Y(t, n || {}, ue, le, e)
    }

    function pe(e, t) {
        var n = {};
        if (!e) return n;
        for (var r = [], i = 0, o = e.length; i < o; i++) {
            var a = e[i];
            if (a.context !== t && a.functionalContext !== t || !a.data || null == a.data.slot) r.push(a); else {
                var s = a.data.slot, c = n[s] || (n[s] = []);
                "template" === a.tag ? c.push.apply(c, a.children) : c.push(a)
            }
        }
        return r.every(de) || (n.default = r), n
    }

    function de(e) {
        return e.isComment || " " === e.text
    }

    function ve(e, t) {
        t = t || {};
        for (var n = 0; n < e.length; n++) Array.isArray(e[n]) ? ve(e[n], t) : t[e[n].key] = e[n].fn;
        return t
    }

    function he(e) {
        var t = e.$options, n = t.parent;
        if (n && !t.abstract) {
            for (; n.$options.abstract && n.$parent;) n = n.$parent;
            n.$children.push(e)
        }
        e.$parent = n, e.$root = n ? n.$root : e, e.$children = [], e.$refs = {}, e._watcher = null, e._inactive = null, e._directInactive = !1, e._isMounted = !1, e._isDestroyed = !1, e._isBeingDestroyed = !1
    }

    function me(e, t, n) {
        e.$el = t, e.$options.render || (e.$options.render = $o), $e(e, "beforeMount");
        var r;
        return r = function () {
            e._update(e._render(), n)
        }, e._watcher = new jo(e, r, y), n = !1, null == e.$vnode && (e._isMounted = !0, $e(e, "mounted")), e
    }

    function ge(e, t, n, r, i) {
        var o = !!(i || e.$options._renderChildren || r.data.scopedSlots || e.$scopedSlots !== Hi);
        if (e.$options._parentVnode = r, e.$vnode = r, e._vnode && (e._vnode.parent = r), e.$options._renderChildren = i, t && e.$options.props) {
            vo.shouldConvert = !1;
            for (var a = e._props, s = e.$options._propKeys || [], c = 0; c < s.length; c++) {
                var u = s[c];
                a[u] = V(u, e.$options.props, t, e)
            }
            vo.shouldConvert = !0, e.$options.propsData = t
        }
        if (n) {
            var l = e.$options._parentListeners;
            e.$options._parentListeners = n, fe(e, n, l)
        }
        o && (e.$slots = pe(i, r.context), e.$forceUpdate())
    }

    function ye(e) {
        for (; e && (e = e.$parent);) if (e._inactive) return !0;
        return !1
    }

    function _e(e, t) {
        if (t) {
            if (e._directInactive = !1, ye(e)) return
        } else if (e._directInactive) return;
        if (e._inactive || null === e._inactive) {
            e._inactive = !1;
            for (var n = 0; n < e.$children.length; n++) _e(e.$children[n]);
            $e(e, "activated")
        }
    }

    function be(e, t) {
        if (!(t && (e._directInactive = !0, ye(e)) || e._inactive)) {
            e._inactive = !0;
            for (var n = 0; n < e.$children.length; n++) be(e.$children[n]);
            $e(e, "deactivated")
        }
    }

    function $e(e, t) {
        var n = e.$options[t];
        if (n) for (var r = 0, i = n.length; r < i; r++) try {
            n[r].call(e)
        } catch (n) {
            k(n, e, t + " hook")
        }
        e._hasHookEvent && e.$emit("hook:" + t)
    }

    function Ce() {
        To = wo.length = ko.length = 0, Ao = {}, Oo = So = !1
    }

    function xe() {
        So = !0;
        var e, t;
        for (wo.sort(function (e, t) {
            return e.id - t.id
        }), To = 0; To < wo.length; To++) e = wo[To], t = e.id, Ao[t] = null, e.run();
        var n = ko.slice(), r = wo.slice();
        Ce(), Ae(n), we(r), io && Bi.devtools && io.emit("flush")
    }

    function we(e) {
        for (var t = e.length; t--;) {
            var n = e[t], r = n.vm;
            r._watcher === n && r._isMounted && $e(r, "updated")
        }
    }

    function ke(e) {
        e._inactive = !1, ko.push(e)
    }

    function Ae(e) {
        for (var t = 0; t < e.length; t++) e[t]._inactive = !0, _e(e[t], !0)
    }

    function Oe(e) {
        var t = e.id;
        if (null == Ao[t]) {
            if (Ao[t] = !0, So) {
                for (var n = wo.length - 1; n > To && wo[n].id > e.id;) n--;
                wo.splice(n + 1, 0, e)
            } else wo.push(e);
            Oo || (Oo = !0, ao(xe))
        }
    }

    function Se(e) {
        No.clear(), Te(e, No)
    }

    function Te(e, t) {
        var n, r, i = Array.isArray(e);
        if ((i || o(e)) && Object.isExtensible(e)) {
            if (e.__ob__) {
                var a = e.__ob__.dep.id;
                if (t.has(a)) return;
                t.add(a)
            }
            if (i) for (n = e.length; n--;) Te(e[n], t); else for (r = Object.keys(e), n = r.length; n--;) Te(e[r[n]], t)
        }
    }

    function Ee(e, t, n) {
        Lo.get = function () {
            return this[t][n]
        }, Lo.set = function (e) {
            this[t][n] = e
        }, Object.defineProperty(e, n, Lo)
    }

    function je(e) {
        e._watchers = [];
        var t = e.$options;
        t.props && Ne(e, t.props), t.methods && Re(e, t.methods), t.data ? Le(e) : j(e._data = {}, !0), t.computed && De(e, t.computed), t.watch && Fe(e, t.watch)
    }

    function Ne(e, t) {
        var n = e.$options.propsData || {}, r = e._props = {}, i = e.$options._propKeys = [], o = !e.$parent;
        vo.shouldConvert = o;
        for (var a in t) !function (o) {
            i.push(o);
            var a = V(o, t, n, e);
            N(r, o, a), o in e || Ee(e, "_props", o)
        }(a);
        vo.shouldConvert = !0
    }

    function Le(e) {
        var t = e.$options.data;
        t = e._data = "function" == typeof t ? Ie(t, e) : t || {}, a(t) || (t = {});
        for (var n = Object.keys(t), r = e.$options.props, i = n.length; i--;) r && p(r, n[i]) || C(n[i]) || Ee(e, "_data", n[i]);
        j(t, !0)
    }

    function Ie(e, t) {
        try {
            return e.call(t)
        } catch (e) {
            return k(e, t, "data()"), {}
        }
    }

    function De(e, t) {
        var n = e._computedWatchers = Object.create(null);
        for (var r in t) {
            var i = t[r], o = "function" == typeof i ? i : i.get;
            n[r] = new jo(e, o, y, Io), r in e || Me(e, r, i)
        }
    }

    function Me(e, t, n) {
        "function" == typeof n ? (Lo.get = Pe(t), Lo.set = y) : (Lo.get = n.get ? !1 !== n.cache ? Pe(t) : n.get : y, Lo.set = n.set ? n.set : y), Object.defineProperty(e, t, Lo)
    }

    function Pe(e) {
        return function () {
            var t = this._computedWatchers && this._computedWatchers[e];
            if (t) return t.dirty && t.evaluate(), co.target && t.depend(), t.value
        }
    }

    function Re(e, t) {
        e.$options.props;
        for (var n in t) e[n] = null == t[n] ? y : v(t[n], e)
    }

    function Fe(e, t) {
        for (var n in t) {
            var r = t[n];
            if (Array.isArray(r)) for (var i = 0; i < r.length; i++) Be(e, n, r[i]); else Be(e, n, r)
        }
    }

    function Be(e, t, n) {
        var r;
        a(n) && (r = n, n = n.handler), "string" == typeof n && (n = e[n]), e.$watch(t, n, r)
    }

    function He(e) {
        var t = e.$options.provide;
        t && (e._provided = "function" == typeof t ? t.call(e) : t)
    }

    function Ue(e) {
        var t = Ve(e.$options.inject, e);
        t && Object.keys(t).forEach(function (n) {
            N(e, n, t[n])
        })
    }

    function Ve(e, t) {
        if (e) {
            for (var n = Array.isArray(e), r = Object.create(null), i = n ? e : oo ? Reflect.ownKeys(e) : Object.keys(e), o = 0; o < i.length; o++) for (var a = i[o], s = n ? a : e[a], c = t; c;) {
                if (c._provided && s in c._provided) {
                    r[a] = c._provided[s];
                    break
                }
                c = c.$parent
            }
            return r
        }
    }

    function ze(e, n, r, i, o) {
        var a = {}, s = e.options.props;
        if (t(s)) for (var c in s) a[c] = V(c, s, n || {}); else t(r.attrs) && Je(a, r.attrs), t(r.props) && Je(a, r.props);
        var u = Object.create(i), l = function (e, t, n, r) {
            return Ye(u, e, t, n, r, !0)
        }, f = e.options.render.call(null, l, {
            data: r,
            props: a,
            children: o,
            parent: i,
            listeners: r.on || {},
            injections: Ve(e.options.inject, i),
            slots: function () {
                return pe(o, i)
            }
        });
        return f instanceof yo && (f.functionalContext = i, f.functionalOptions = e.options, r.slot && ((f.data || (f.data = {})).slot = r.slot)), f
    }

    function Je(e, t) {
        for (var n in t) e[Ni(n)] = t[n]
    }

    function Ke(r, i, a, s, c) {
        if (!e(r)) {
            var u = a.$options._base;
            if (o(r) && (r = u.extend(r)), "function" == typeof r && (!e(r.cid) || void 0 !== (r = ae(r, u, a)))) {
                ft(r), i = i || {}, t(i.model) && Ge(r.options, i);
                var l = X(i, r, c);
                if (n(r.options.functional)) return ze(r, l, i, a, s);
                var f = i.on;
                i.on = i.nativeOn, n(r.options.abstract) && (i = {}), We(i);
                var p = r.options.name || c;
                return new yo("vue-component-" + r.cid + (p ? "-" + p : ""), i, void 0, void 0, void 0, a, {
                    Ctor: r,
                    propsData: l,
                    listeners: f,
                    tag: c,
                    children: s
                })
            }
        }
    }

    function qe(e, n, r, i) {
        var o = e.componentOptions, a = {
            _isComponent: !0,
            parent: n,
            propsData: o.propsData,
            _componentTag: o.tag,
            _parentVnode: e,
            _parentListeners: o.listeners,
            _renderChildren: o.children,
            _parentElm: r || null,
            _refElm: i || null
        }, s = e.data.inlineTemplate;
        return t(s) && (a.render = s.render, a.staticRenderFns = s.staticRenderFns), new o.Ctor(a)
    }

    function We(e) {
        e.hook || (e.hook = {});
        for (var t = 0; t < Mo.length; t++) {
            var n = Mo[t], r = e.hook[n], i = Do[n];
            e.hook[n] = r ? Ze(i, r) : i
        }
    }

    function Ze(e, t) {
        return function (n, r, i, o) {
            e(n, r, i, o), t(n, r, i, o)
        }
    }

    function Ge(e, n) {
        var r = e.model && e.model.prop || "value", i = e.model && e.model.event || "input";
        (n.props || (n.props = {}))[r] = n.model.value;
        var o = n.on || (n.on = {});
        t(o[i]) ? o[i] = [n.model.callback].concat(o[i]) : o[i] = n.model.callback
    }

    function Ye(e, t, r, o, a, s) {
        return (Array.isArray(r) || i(r)) && (a = o, o = r, r = void 0), n(s) && (a = Ro), Qe(e, t, r, o, a)
    }

    function Qe(e, n, r, i, o) {
        if (t(r) && t(r.__ob__)) return $o();
        if (!n) return $o();
        Array.isArray(i) && "function" == typeof i[0] && (r = r || {}, r.scopedSlots = {default: i[0]}, i.length = 0), o === Ro ? i = ne(i) : o === Po && (i = te(i));
        var a, s;
        if ("string" == typeof n) {
            var c;
            s = Bi.getTagNamespace(n), a = Bi.isReservedTag(n) ? new yo(Bi.parsePlatformTagName(n), r, i, void 0, void 0, e) : t(c = U(e.$options, "components", n)) ? Ke(c, r, e, i, n) : new yo(n, r, i, void 0, void 0, e)
        } else a = Ke(n, r, e, i);
        return t(a) ? (s && Xe(a, s), a) : $o()
    }

    function Xe(n, r) {
        if (n.ns = r, "foreignObject" !== n.tag && t(n.children)) for (var i = 0, o = n.children.length; i < o; i++) {
            var a = n.children[i];
            t(a.tag) && e(a.ns) && Xe(a, r)
        }
    }

    function et(e, n) {
        var r, i, a, s, c;
        if (Array.isArray(e) || "string" == typeof e) for (r = new Array(e.length), i = 0, a = e.length; i < a; i++) r[i] = n(e[i], i); else if ("number" == typeof e) for (r = new Array(e), i = 0; i < e; i++) r[i] = n(i + 1, i); else if (o(e)) for (s = Object.keys(e), r = new Array(s.length), i = 0, a = s.length; i < a; i++) c = s[i], r[i] = n(e[c], c, i);
        return t(r) && (r._isVList = !0), r
    }

    function tt(e, t, n, r) {
        var i = this.$scopedSlots[e];
        if (i) return n = n || {}, r && m(n, r), i(n) || t;
        var o = this.$slots[e];
        return o || t
    }

    function nt(e) {
        return U(this.$options, "filters", e, !0) || Mi
    }

    function rt(e, t, n) {
        var r = Bi.keyCodes[t] || n;
        return Array.isArray(r) ? -1 === r.indexOf(e) : r !== e
    }

    function it(e, t, n, r) {
        if (n) if (o(n)) {
            Array.isArray(n) && (n = g(n));
            var i;
            for (var a in n) {
                if ("class" === a || "style" === a) i = e; else {
                    var s = e.attrs && e.attrs.type;
                    i = r || Bi.mustUseProp(t, s, a) ? e.domProps || (e.domProps = {}) : e.attrs || (e.attrs = {})
                }
                a in i || (i[a] = n[a])
            }
        } else ;
        return e
    }

    function ot(e, t) {
        var n = this._staticTrees[e];
        return n && !t ? Array.isArray(n) ? Z(n) : W(n) : (n = this._staticTrees[e] = this.$options.staticRenderFns[e].call(this._renderProxy), st(n, "__static__" + e, !1), n)
    }

    function at(e, t, n) {
        return st(e, "__once__" + t + (n ? "_" + n : ""), !0), e
    }

    function st(e, t, n) {
        if (Array.isArray(e)) for (var r = 0; r < e.length; r++) e[r] && "string" != typeof e[r] && ct(e[r], t + "_" + r, n); else ct(e, t, n)
    }

    function ct(e, t, n) {
        e.isStatic = !0, e.key = t, e.isOnce = n
    }

    function ut(e) {
        e._vnode = null, e._staticTrees = null;
        var t = e.$vnode = e.$options._parentVnode, n = t && t.context;
        e.$slots = pe(e.$options._renderChildren, n), e.$scopedSlots = Hi, e._c = function (t, n, r, i) {
            return Ye(e, t, n, r, i, !1)
        }, e.$createElement = function (t, n, r, i) {
            return Ye(e, t, n, r, i, !0)
        }
    }

    function lt(e, t) {
        var n = e.$options = Object.create(e.constructor.options);
        n.parent = t.parent, n.propsData = t.propsData, n._parentVnode = t._parentVnode, n._parentListeners = t._parentListeners, n._renderChildren = t._renderChildren, n._componentTag = t._componentTag, n._parentElm = t._parentElm, n._refElm = t._refElm, t.render && (n.render = t.render, n.staticRenderFns = t.staticRenderFns)
    }

    function ft(e) {
        var t = e.options;
        if (e.super) {
            var n = ft(e.super);
            if (n !== e.superOptions) {
                e.superOptions = n;
                var r = pt(e);
                r && m(e.extendOptions, r), t = e.options = H(n, e.extendOptions), t.name && (t.components[t.name] = e)
            }
        }
        return t
    }

    function pt(e) {
        var t, n = e.options, r = e.extendOptions, i = e.sealedOptions;
        for (var o in n) n[o] !== i[o] && (t || (t = {}), t[o] = dt(n[o], r[o], i[o]));
        return t
    }

    function dt(e, t, n) {
        if (Array.isArray(e)) {
            var r = [];
            n = Array.isArray(n) ? n : [n], t = Array.isArray(t) ? t : [t];
            for (var i = 0; i < e.length; i++) (t.indexOf(e[i]) >= 0 || n.indexOf(e[i]) < 0) && r.push(e[i]);
            return r
        }
        return e
    }

    function vt(e) {
        this._init(e)
    }

    function ht(e) {
        e.use = function (e) {
            if (e.installed) return this;
            var t = h(arguments, 1);
            return t.unshift(this), "function" == typeof e.install ? e.install.apply(e, t) : "function" == typeof e && e.apply(null, t), e.installed = !0, this
        }
    }

    function mt(e) {
        e.mixin = function (e) {
            return this.options = H(this.options, e), this
        }
    }

    function gt(e) {
        e.cid = 0;
        var t = 1;
        e.extend = function (e) {
            e = e || {};
            var n = this, r = n.cid, i = e._Ctor || (e._Ctor = {});
            if (i[r]) return i[r];
            var o = e.name || n.options.name, a = function (e) {
                this._init(e)
            };
            return a.prototype = Object.create(n.prototype), a.prototype.constructor = a, a.cid = t++, a.options = H(n.options, e), a.super = n, a.options.props && yt(a), a.options.computed && _t(a), a.extend = n.extend, a.mixin = n.mixin, a.use = n.use, Ri.forEach(function (e) {
                a[e] = n[e]
            }), o && (a.options.components[o] = a), a.superOptions = n.options, a.extendOptions = e, a.sealedOptions = m({}, a.options), i[r] = a, a
        }
    }

    function yt(e) {
        var t = e.options.props;
        for (var n in t) Ee(e.prototype, "_props", n)
    }

    function _t(e) {
        var t = e.options.computed;
        for (var n in t) Me(e.prototype, n, t[n])
    }

    function bt(e) {
        Ri.forEach(function (t) {
            e[t] = function (e, n) {
                return n ? ("component" === t && a(n) && (n.name = n.name || e, n = this.options._base.extend(n)), "directive" === t && "function" == typeof n && (n = {
                    bind: n,
                    update: n
                }), this.options[t + "s"][e] = n, n) : this.options[t + "s"][e]
            }
        })
    }

    function $t(e) {
        return e && (e.Ctor.options.name || e.tag)
    }

    function Ct(e, t) {
        return "string" == typeof e ? e.split(",").indexOf(t) > -1 : !!s(e) && e.test(t)
    }

    function xt(e, t, n) {
        for (var r in e) {
            var i = e[r];
            if (i) {
                var o = $t(i.componentOptions);
                o && !n(o) && (i !== t && wt(i), e[r] = null)
            }
        }
    }

    function wt(e) {
        e && e.componentInstance.$destroy()
    }

    function kt(e) {
        for (var n = e.data, r = e, i = e; t(i.componentInstance);) i = i.componentInstance._vnode, i.data && (n = At(i.data, n));
        for (; t(r = r.parent);) r.data && (n = At(n, r.data));
        return Ot(n)
    }

    function At(e, n) {
        return {staticClass: St(e.staticClass, n.staticClass), class: t(e.class) ? [e.class, n.class] : n.class}
    }

    function Ot(e) {
        var n = e.class, r = e.staticClass;
        return t(r) || t(n) ? St(r, Tt(n)) : ""
    }

    function St(e, t) {
        return e ? t ? e + " " + t : e : t || ""
    }

    function Tt(n) {
        if (e(n)) return "";
        if ("string" == typeof n) return n;
        var r = "";
        if (Array.isArray(n)) {
            for (var i, a = 0, s = n.length; a < s; a++) t(n[a]) && t(i = Tt(n[a])) && "" !== i && (r += i + " ");
            return r.slice(0, -1)
        }
        if (o(n)) {
            for (var c in n) n[c] && (r += c + " ");
            return r.slice(0, -1)
        }
        return r
    }

    function Et(e) {
        return ua(e) ? "svg" : "math" === e ? "math" : void 0
    }

    function jt(e) {
        if (!Ji) return !0;
        if (fa(e)) return !1;
        if (e = e.toLowerCase(), null != pa[e]) return pa[e];
        var t = document.createElement(e);
        return e.indexOf("-") > -1 ? pa[e] = t.constructor === window.HTMLUnknownElement || t.constructor === window.HTMLElement : pa[e] = /HTMLUnknownElement/.test(t.toString())
    }

    function Nt(e) {
        if ("string" == typeof e) {
            var t = document.querySelector(e);
            return t || document.createElement("div")
        }
        return e
    }

    function Lt(e, t) {
        var n = document.createElement(e);
        return "select" !== e ? n : (t.data && t.data.attrs && void 0 !== t.data.attrs.multiple && n.setAttribute("multiple", "multiple"), n)
    }

    function It(e, t) {
        return document.createElementNS(sa[e], t)
    }

    function Dt(e) {
        return document.createTextNode(e)
    }

    function Mt(e) {
        return document.createComment(e)
    }

    function Pt(e, t, n) {
        e.insertBefore(t, n)
    }

    function Rt(e, t) {
        e.removeChild(t)
    }

    function Ft(e, t) {
        e.appendChild(t)
    }

    function Bt(e) {
        return e.parentNode
    }

    function Ht(e) {
        return e.nextSibling
    }

    function Ut(e) {
        return e.tagName
    }

    function Vt(e, t) {
        e.textContent = t
    }

    function zt(e, t, n) {
        e.setAttribute(t, n)
    }

    function Jt(e, t) {
        var n = e.data.ref;
        if (n) {
            var r = e.context, i = e.componentInstance || e.elm, o = r.$refs;
            t ? Array.isArray(o[n]) ? f(o[n], i) : o[n] === i && (o[n] = void 0) : e.data.refInFor ? Array.isArray(o[n]) && o[n].indexOf(i) < 0 ? o[n].push(i) : o[n] = [i] : o[n] = i
        }
    }

    function Kt(e, n) {
        return e.key === n.key && e.tag === n.tag && e.isComment === n.isComment && t(e.data) === t(n.data) && qt(e, n)
    }

    function qt(e, n) {
        if ("input" !== e.tag) return !0;
        var r;
        return (t(r = e.data) && t(r = r.attrs) && r.type) === (t(r = n.data) && t(r = r.attrs) && r.type)
    }

    function Wt(e, n, r) {
        var i, o, a = {};
        for (i = n; i <= r; ++i) o = e[i].key, t(o) && (a[o] = i);
        return a
    }

    function Zt(e, t) {
        (e.data.directives || t.data.directives) && Gt(e, t)
    }

    function Gt(e, t) {
        var n, r, i, o = e === ha, a = t === ha, s = Yt(e.data.directives, e.context),
            c = Yt(t.data.directives, t.context), u = [], l = [];
        for (n in c) r = s[n], i = c[n], r ? (i.oldValue = r.value, Xt(i, "update", t, e), i.def && i.def.componentUpdated && l.push(i)) : (Xt(i, "bind", t, e), i.def && i.def.inserted && u.push(i));
        if (u.length) {
            var f = function () {
                for (var n = 0; n < u.length; n++) Xt(u[n], "inserted", t, e)
            };
            o ? Q(t.data.hook || (t.data.hook = {}), "insert", f) : f()
        }
        if (l.length && Q(t.data.hook || (t.data.hook = {}), "postpatch", function () {
                for (var n = 0; n < l.length; n++) Xt(l[n], "componentUpdated", t, e)
            }), !o) for (n in s) c[n] || Xt(s[n], "unbind", e, e, a)
    }

    function Yt(e, t) {
        var n = Object.create(null);
        if (!e) return n;
        var r, i;
        for (r = 0; r < e.length; r++) i = e[r], i.modifiers || (i.modifiers = ya), n[Qt(i)] = i, i.def = U(t.$options, "directives", i.name, !0);
        return n
    }

    function Qt(e) {
        return e.rawName || e.name + "." + Object.keys(e.modifiers || {}).join(".")
    }

    function Xt(e, t, n, r, i) {
        var o = e.def && e.def[t];
        if (o) try {
            o(n.elm, e, n, r, i)
        } catch (r) {
            k(r, n.context, "directive " + e.name + " " + t + " hook")
        }
    }

    function en(n, r) {
        if (!e(n.data.attrs) || !e(r.data.attrs)) {
            var i, o, a = r.elm, s = n.data.attrs || {}, c = r.data.attrs || {};
            t(c.__ob__) && (c = r.data.attrs = m({}, c));
            for (i in c) o = c[i], s[i] !== o && tn(a, i, o);
            Wi && c.value !== s.value && tn(a, "value", c.value);
            for (i in s) e(c[i]) && (ia(i) ? a.removeAttributeNS(ra, oa(i)) : ta(i) || a.removeAttribute(i))
        }
    }

    function tn(e, t, n) {
        na(t) ? aa(n) ? e.removeAttribute(t) : e.setAttribute(t, t) : ta(t) ? e.setAttribute(t, aa(n) || "false" === n ? "false" : "true") : ia(t) ? aa(n) ? e.removeAttributeNS(ra, oa(t)) : e.setAttributeNS(ra, t, n) : aa(n) ? e.removeAttribute(t) : e.setAttribute(t, n)
    }

    function nn(n, r) {
        var i = r.elm, o = r.data, a = n.data;
        if (!(e(o.staticClass) && e(o.class) && (e(a) || e(a.staticClass) && e(a.class)))) {
            var s = kt(r), c = i._transitionClasses;
            t(c) && (s = St(s, Tt(c))), s !== i._prevClass && (i.setAttribute("class", s), i._prevClass = s)
        }
    }

    function rn(e) {
        function t() {
            (a || (a = [])).push(e.slice(v, i).trim()), v = i + 1
        }

        var n, r, i, o, a, s = !1, c = !1, u = !1, l = !1, f = 0, p = 0, d = 0, v = 0;
        for (i = 0; i < e.length; i++) if (r = n, n = e.charCodeAt(i), s) 39 === n && 92 !== r && (s = !1); else if (c) 34 === n && 92 !== r && (c = !1); else if (u) 96 === n && 92 !== r && (u = !1); else if (l) 47 === n && 92 !== r && (l = !1); else if (124 !== n || 124 === e.charCodeAt(i + 1) || 124 === e.charCodeAt(i - 1) || f || p || d) {
            switch (n) {
                case 34:
                    c = !0;
                    break;
                case 39:
                    s = !0;
                    break;
                case 96:
                    u = !0;
                    break;
                case 40:
                    d++;
                    break;
                case 41:
                    d--;
                    break;
                case 91:
                    p++;
                    break;
                case 93:
                    p--;
                    break;
                case 123:
                    f++;
                    break;
                case 125:
                    f--
            }
            if (47 === n) {
                for (var h = i - 1, m = void 0; h >= 0 && " " === (m = e.charAt(h)); h--) ;
                m && Ca.test(m) || (l = !0)
            }
        } else void 0 === o ? (v = i + 1, o = e.slice(0, i).trim()) : t();
        if (void 0 === o ? o = e.slice(0, i).trim() : 0 !== v && t(), a) for (i = 0; i < a.length; i++) o = on(o, a[i]);
        return o
    }

    function on(e, t) {
        var n = t.indexOf("(");
        return n < 0 ? '_f("' + t + '")(' + e + ")" : '_f("' + t.slice(0, n) + '")(' + e + "," + t.slice(n + 1)
    }

    function an(e) {
        console.error("[Vue compiler]: " + e)
    }

    function sn(e, t) {
        return e ? e.map(function (e) {
            return e[t]
        }).filter(function (e) {
            return e
        }) : []
    }

    function cn(e, t, n) {
        (e.props || (e.props = [])).push({name: t, value: n})
    }

    function un(e, t, n) {
        (e.attrs || (e.attrs = [])).push({name: t, value: n})
    }

    function ln(e, t, n, r, i, o) {
        (e.directives || (e.directives = [])).push({name: t, rawName: n, value: r, arg: i, modifiers: o})
    }

    function fn(e, t, n, r, i, o) {
        r && r.capture && (delete r.capture, t = "!" + t), r && r.once && (delete r.once, t = "~" + t), r && r.passive && (delete r.passive, t = "&" + t);
        var a;
        r && r.native ? (delete r.native, a = e.nativeEvents || (e.nativeEvents = {})) : a = e.events || (e.events = {});
        var s = {value: n, modifiers: r}, c = a[t];
        Array.isArray(c) ? i ? c.unshift(s) : c.push(s) : a[t] = c ? i ? [s, c] : [c, s] : s
    }

    function pn(e, t, n) {
        var r = dn(e, ":" + t) || dn(e, "v-bind:" + t);
        if (null != r) return rn(r);
        if (!1 !== n) {
            var i = dn(e, t);
            if (null != i) return JSON.stringify(i)
        }
    }

    function dn(e, t) {
        var n;
        if (null != (n = e.attrsMap[t])) for (var r = e.attrsList, i = 0, o = r.length; i < o; i++) if (r[i].name === t) {
            r.splice(i, 1);
            break
        }
        return n
    }

    function vn(e, t, n) {
        var r = n || {}, i = r.number, o = r.trim, a = "$$v";
        o && (a = "(typeof $$v === 'string'? $$v.trim(): $$v)"), i && (a = "_n(" + a + ")");
        var s = hn(t, a);
        e.model = {value: "(" + t + ")", expression: '"' + t + '"', callback: "function ($$v) {" + s + "}"}
    }

    function hn(e, t) {
        var n = mn(e);
        return null === n.idx ? e + "=" + t : "var $$exp = " + n.exp + ", $$idx = " + n.idx + ";if (!Array.isArray($$exp)){" + e + "=" + t + "}else{$$exp.splice($$idx, 1, " + t + ")}"
    }

    function mn(e) {
        if (zo = e, Vo = zo.length, Ko = qo = Wo = 0, e.indexOf("[") < 0 || e.lastIndexOf("]") < Vo - 1) return {
            exp: e,
            idx: null
        };
        for (; !yn();) Jo = gn(), _n(Jo) ? $n(Jo) : 91 === Jo && bn(Jo);
        return {exp: e.substring(0, qo), idx: e.substring(qo + 1, Wo)}
    }

    function gn() {
        return zo.charCodeAt(++Ko)
    }

    function yn() {
        return Ko >= Vo
    }

    function _n(e) {
        return 34 === e || 39 === e
    }

    function bn(e) {
        var t = 1;
        for (qo = Ko; !yn();) if (e = gn(), _n(e)) $n(e); else if (91 === e && t++, 93 === e && t--, 0 === t) {
            Wo = Ko;
            break
        }
    }

    function $n(e) {
        for (var t = e; !yn() && (e = gn()) !== t;) ;
    }

    function Cn(e, t, n) {
        Zo = n;
        var r = t.value, i = t.modifiers, o = e.tag, a = e.attrsMap.type;
        if ("select" === o) kn(e, r, i); else if ("input" === o && "checkbox" === a) xn(e, r, i); else if ("input" === o && "radio" === a) wn(e, r, i); else if ("input" === o || "textarea" === o) An(e, r, i); else if (!Bi.isReservedTag(o)) return vn(e, r, i), !1;
        return !0
    }

    function xn(e, t, n) {
        var r = n && n.number, i = pn(e, "value") || "null", o = pn(e, "true-value") || "true",
            a = pn(e, "false-value") || "false";
        cn(e, "checked", "Array.isArray(" + t + ")?_i(" + t + "," + i + ")>-1" + ("true" === o ? ":(" + t + ")" : ":_q(" + t + "," + o + ")")), fn(e, wa, "var $$a=" + t + ",$$el=$event.target,$$c=$$el.checked?(" + o + "):(" + a + ");if(Array.isArray($$a)){var $$v=" + (r ? "_n(" + i + ")" : i) + ",$$i=_i($$a,$$v);if($$c){$$i<0&&(" + t + "=$$a.concat($$v))}else{$$i>-1&&(" + t + "=$$a.slice(0,$$i).concat($$a.slice($$i+1)))}}else{" + hn(t, "$$c") + "}", null, !0)
    }

    function wn(e, t, n) {
        var r = n && n.number, i = pn(e, "value") || "null";
        i = r ? "_n(" + i + ")" : i, cn(e, "checked", "_q(" + t + "," + i + ")"), fn(e, wa, hn(t, i), null, !0)
    }

    function kn(e, t, n) {
        var r = n && n.number,
            i = 'Array.prototype.filter.call($event.target.options,function(o){return o.selected}).map(function(o){var val = "_value" in o ? o._value : o.value;return ' + (r ? "_n(val)" : "val") + "})",
            o = "var $$selectedVal = " + i + ";";
        o = o + " " + hn(t, "$event.target.multiple ? $$selectedVal : $$selectedVal[0]"), fn(e, "change", o, null, !0)
    }

    function An(e, t, n) {
        var r = e.attrsMap.type, i = n || {}, o = i.lazy, a = i.number, s = i.trim, c = !o && "range" !== r,
            u = o ? "change" : "range" === r ? xa : "input", l = "$event.target.value";
        s && (l = "$event.target.value.trim()"), a && (l = "_n(" + l + ")");
        var f = hn(t, l);
        c && (f = "if($event.target.composing)return;" + f), cn(e, "value", "(" + t + ")"), fn(e, u, f, null, !0), (s || a || "number" === r) && fn(e, "blur", "$forceUpdate()")
    }

    function On(e) {
        var n;
        t(e[xa]) && (n = qi ? "change" : "input", e[n] = [].concat(e[xa], e[n] || []), delete e[xa]), t(e[wa]) && (n = Qi ? "click" : "change", e[n] = [].concat(e[wa], e[n] || []), delete e[wa])
    }

    function Sn(e, t, n, r, i) {
        if (n) {
            var o = t, a = Go;
            t = function (n) {
                null !== (1 === arguments.length ? o(n) : o.apply(null, arguments)) && Tn(e, t, r, a)
            }
        }
        Go.addEventListener(e, t, Xi ? {capture: r, passive: i} : r)
    }

    function Tn(e, t, n, r) {
        (r || Go).removeEventListener(e, t, n)
    }

    function En(t, n) {
        if (!e(t.data.on) || !e(n.data.on)) {
            var r = n.data.on || {}, i = t.data.on || {};
            Go = n.elm, On(r), Y(r, i, Sn, Tn, n.context)
        }
    }

    function jn(n, r) {
        if (!e(n.data.domProps) || !e(r.data.domProps)) {
            var i, o, a = r.elm, s = n.data.domProps || {}, c = r.data.domProps || {};
            t(c.__ob__) && (c = r.data.domProps = m({}, c));
            for (i in s) e(c[i]) && (a[i] = "");
            for (i in c) if (o = c[i], "textContent" !== i && "innerHTML" !== i || (r.children && (r.children.length = 0), o !== s[i])) if ("value" === i) {
                a._value = o;
                var u = e(o) ? "" : String(o);
                Nn(a, r, u) && (a.value = u)
            } else a[i] = o
        }
    }

    function Nn(e, t, n) {
        return !e.composing && ("option" === t.tag || Ln(e, n) || In(e, n))
    }

    function Ln(e, t) {
        return document.activeElement !== e && e.value !== t
    }

    function In(e, n) {
        var r = e.value, i = e._vModifiers;
        return t(i) && i.number || "number" === e.type ? u(r) !== u(n) : t(i) && i.trim ? r.trim() !== n.trim() : r !== n
    }

    function Dn(e) {
        var t = Mn(e.style);
        return e.staticStyle ? m(e.staticStyle, t) : t
    }

    function Mn(e) {
        return Array.isArray(e) ? g(e) : "string" == typeof e ? Oa(e) : e
    }

    function Pn(e, t) {
        var n, r = {};
        if (t) for (var i = e; i.componentInstance;) i = i.componentInstance._vnode, i.data && (n = Dn(i.data)) && m(r, n);
        (n = Dn(e.data)) && m(r, n);
        for (var o = e; o = o.parent;) o.data && (n = Dn(o.data)) && m(r, n);
        return r
    }

    function Rn(n, r) {
        var i = r.data, o = n.data;
        if (!(e(i.staticStyle) && e(i.style) && e(o.staticStyle) && e(o.style))) {
            var a, s, c = r.elm, u = o.staticStyle, l = o.normalizedStyle || o.style || {}, f = u || l,
                p = Mn(r.data.style) || {};
            r.data.normalizedStyle = t(p.__ob__) ? m({}, p) : p;
            var d = Pn(r, !0);
            for (s in f) e(d[s]) && Ea(c, s, "");
            for (s in d) (a = d[s]) !== f[s] && Ea(c, s, null == a ? "" : a)
        }
    }

    function Fn(e, t) {
        if (t && (t = t.trim())) if (e.classList) t.indexOf(" ") > -1 ? t.split(/\s+/).forEach(function (t) {
            return e.classList.add(t)
        }) : e.classList.add(t); else {
            var n = " " + (e.getAttribute("class") || "") + " ";
            n.indexOf(" " + t + " ") < 0 && e.setAttribute("class", (n + t).trim())
        }
    }

    function Bn(e, t) {
        if (t && (t = t.trim())) if (e.classList) t.indexOf(" ") > -1 ? t.split(/\s+/).forEach(function (t) {
            return e.classList.remove(t)
        }) : e.classList.remove(t); else {
            for (var n = " " + (e.getAttribute("class") || "") + " ", r = " " + t + " "; n.indexOf(r) >= 0;) n = n.replace(r, " ");
            e.setAttribute("class", n.trim())
        }
    }

    function Hn(e) {
        if (e) {
            if ("object" == typeof e) {
                var t = {};
                return !1 !== e.css && m(t, Ia(e.name || "v")), m(t, e), t
            }
            return "string" == typeof e ? Ia(e) : void 0
        }
    }

    function Un(e) {
        Ua(function () {
            Ua(e)
        })
    }

    function Vn(e, t) {
        (e._transitionClasses || (e._transitionClasses = [])).push(t), Fn(e, t)
    }

    function zn(e, t) {
        e._transitionClasses && f(e._transitionClasses, t), Bn(e, t)
    }

    function Jn(e, t, n) {
        var r = Kn(e, t), i = r.type, o = r.timeout, a = r.propCount;
        if (!i) return n();
        var s = i === Ma ? Fa : Ha, c = 0, u = function () {
            e.removeEventListener(s, l), n()
        }, l = function (t) {
            t.target === e && ++c >= a && u()
        };
        setTimeout(function () {
            c < a && u()
        }, o + 1), e.addEventListener(s, l)
    }

    function Kn(e, t) {
        var n, r = window.getComputedStyle(e), i = r[Ra + "Delay"].split(", "), o = r[Ra + "Duration"].split(", "),
            a = qn(i, o), s = r[Ba + "Delay"].split(", "), c = r[Ba + "Duration"].split(", "), u = qn(s, c), l = 0,
            f = 0;
        return t === Ma ? a > 0 && (n = Ma, l = a, f = o.length) : t === Pa ? u > 0 && (n = Pa, l = u, f = c.length) : (l = Math.max(a, u), n = l > 0 ? a > u ? Ma : Pa : null, f = n ? n === Ma ? o.length : c.length : 0), {
            type: n,
            timeout: l,
            propCount: f,
            hasTransform: n === Ma && Va.test(r[Ra + "Property"])
        }
    }

    function qn(e, t) {
        for (; e.length < t.length;) e = e.concat(e);
        return Math.max.apply(null, t.map(function (t, n) {
            return Wn(t) + Wn(e[n])
        }))
    }

    function Wn(e) {
        return 1e3 * Number(e.slice(0, -1))
    }

    function Zn(n, r) {
        var i = n.elm;
        t(i._leaveCb) && (i._leaveCb.cancelled = !0, i._leaveCb());
        var a = Hn(n.data.transition);
        if (!e(a) && !t(i._enterCb) && 1 === i.nodeType) {
            for (var s = a.css, c = a.type, l = a.enterClass, f = a.enterToClass, p = a.enterActiveClass, d = a.appearClass, v = a.appearToClass, h = a.appearActiveClass, m = a.beforeEnter, g = a.enter, y = a.afterEnter, _ = a.enterCancelled, b = a.beforeAppear, C = a.appear, x = a.afterAppear, w = a.appearCancelled, k = a.duration, A = xo, O = xo.$vnode; O && O.parent;) O = O.parent, A = O.context;
            var S = !A._isMounted || !n.isRootInsert;
            if (!S || C || "" === C) {
                var T = S && d ? d : l, E = S && h ? h : p, j = S && v ? v : f, N = S ? b || m : m,
                    L = S && "function" == typeof C ? C : g, I = S ? x || y : y, D = S ? w || _ : _,
                    M = u(o(k) ? k.enter : k), P = !1 !== s && !Wi, R = Qn(L), F = i._enterCb = $(function () {
                        P && (zn(i, j), zn(i, E)), F.cancelled ? (P && zn(i, T), D && D(i)) : I && I(i), i._enterCb = null
                    });
                n.data.show || Q(n.data.hook || (n.data.hook = {}), "insert", function () {
                    var e = i.parentNode, t = e && e._pending && e._pending[n.key];
                    t && t.tag === n.tag && t.elm._leaveCb && t.elm._leaveCb(), L && L(i, F)
                }), N && N(i), P && (Vn(i, T), Vn(i, E), Un(function () {
                    Vn(i, j), zn(i, T), F.cancelled || R || (Yn(M) ? setTimeout(F, M) : Jn(i, c, F))
                })), n.data.show && (r && r(), L && L(i, F)), P || R || F()
            }
        }
    }

    function Gn(n, r) {
        function i() {
            w.cancelled || (n.data.show || ((a.parentNode._pending || (a.parentNode._pending = {}))[n.key] = n), v && v(a), b && (Vn(a, f), Vn(a, d), Un(function () {
                Vn(a, p), zn(a, f), w.cancelled || C || (Yn(x) ? setTimeout(w, x) : Jn(a, l, w))
            })), h && h(a, w), b || C || w())
        }

        var a = n.elm;
        t(a._enterCb) && (a._enterCb.cancelled = !0, a._enterCb());
        var s = Hn(n.data.transition);
        if (e(s)) return r();
        if (!t(a._leaveCb) && 1 === a.nodeType) {
            var c = s.css, l = s.type, f = s.leaveClass, p = s.leaveToClass, d = s.leaveActiveClass, v = s.beforeLeave,
                h = s.leave, m = s.afterLeave, g = s.leaveCancelled, y = s.delayLeave, _ = s.duration,
                b = !1 !== c && !Wi, C = Qn(h), x = u(o(_) ? _.leave : _), w = a._leaveCb = $(function () {
                    a.parentNode && a.parentNode._pending && (a.parentNode._pending[n.key] = null), b && (zn(a, p), zn(a, d)), w.cancelled ? (b && zn(a, f), g && g(a)) : (r(), m && m(a)), a._leaveCb = null
                });
            y ? y(i) : i()
        }
    }

    function Yn(e) {
        return "number" == typeof e && !isNaN(e)
    }

    function Qn(n) {
        if (e(n)) return !1;
        var r = n.fns;
        return t(r) ? Qn(Array.isArray(r) ? r[0] : r) : (n._length || n.length) > 1
    }

    function Xn(e, t) {
        !0 !== t.data.show && Zn(t)
    }

    function er(e, t, n) {
        var r = t.value, i = e.multiple;
        if (!i || Array.isArray(r)) {
            for (var o, a, s = 0, c = e.options.length; s < c; s++) if (a = e.options[s], i) o = b(r, nr(a)) > -1, a.selected !== o && (a.selected = o); else if (_(nr(a), r)) return void(e.selectedIndex !== s && (e.selectedIndex = s));
            i || (e.selectedIndex = -1)
        }
    }

    function tr(e, t) {
        for (var n = 0, r = t.length; n < r; n++) if (_(nr(t[n]), e)) return !1;
        return !0
    }

    function nr(e) {
        return "_value" in e ? e._value : e.value
    }

    function rr(e) {
        e.target.composing = !0
    }

    function ir(e) {
        e.target.composing && (e.target.composing = !1, or(e.target, "input"))
    }

    function or(e, t) {
        var n = document.createEvent("HTMLEvents");
        n.initEvent(t, !0, !0), e.dispatchEvent(n)
    }

    function ar(e) {
        return !e.componentInstance || e.data && e.data.transition ? e : ar(e.componentInstance._vnode)
    }

    function sr(e) {
        var t = e && e.componentOptions;
        return t && t.Ctor.options.abstract ? sr(se(t.children)) : e
    }

    function cr(e) {
        var t = {}, n = e.$options;
        for (var r in n.propsData) t[r] = e[r];
        var i = n._parentListeners;
        for (var o in i) t[Ni(o)] = i[o];
        return t
    }

    function ur(e, t) {
        if (/\d-keep-alive$/.test(t.tag)) return e("keep-alive", {props: t.componentOptions.propsData})
    }

    function lr(e) {
        for (; e = e.parent;) if (e.data.transition) return !0
    }

    function fr(e, t) {
        return t.key === e.key && t.tag === e.tag
    }

    function pr(e) {
        e.elm._moveCb && e.elm._moveCb(), e.elm._enterCb && e.elm._enterCb()
    }

    function dr(e) {
        e.data.newPos = e.elm.getBoundingClientRect()
    }

    function vr(e) {
        var t = e.data.pos, n = e.data.newPos, r = t.left - n.left, i = t.top - n.top;
        if (r || i) {
            e.data.moved = !0;
            var o = e.elm.style;
            o.transform = o.WebkitTransform = "translate(" + r + "px," + i + "px)", o.transitionDuration = "0s"
        }
    }

    function hr(e) {
        return ns = ns || document.createElement("div"), ns.innerHTML = e, ns.textContent
    }

    function mr(e, t) {
        var n = t ? Fs : Rs;
        return e.replace(n, function (e) {
            return Ps[e]
        })
    }

    function gr(e, t) {
        function n(t) {
            l += t, e = e.substring(t)
        }

        function r(e, n, r) {
            var i, s;
            if (null == n && (n = l), null == r && (r = l), e && (s = e.toLowerCase()), e) for (i = a.length - 1; i >= 0 && a[i].lowerCasedTag !== s; i--) ; else i = 0;
            if (i >= 0) {
                for (var c = a.length - 1; c >= i; c--) t.end && t.end(a[c].tag, n, r);
                a.length = i, o = i && a[i - 1].tag
            } else "br" === s ? t.start && t.start(e, [], !0, n, r) : "p" === s && (t.start && t.start(e, [], !1, n, r), t.end && t.end(e, n, r))
        }

        for (var i, o, a = [], s = t.expectHTML, c = t.isUnaryTag || Di, u = t.canBeLeftOpenTag || Di, l = 0; e;) {
            if (i = e, o && Ds(o)) {
                var f = o.toLowerCase(), p = Ms[f] || (Ms[f] = new RegExp("([\\s\\S]*?)(</" + f + "[^>]*>)", "i")),
                    d = 0, v = e.replace(p, function (e, n, r) {
                        return d = r.length, Ds(f) || "noscript" === f || (n = n.replace(/<!--([\s\S]*?)-->/g, "$1").replace(/<!\[CDATA\[([\s\S]*?)]]>/g, "$1")), t.chars && t.chars(n), ""
                    });
                l += e.length - v.length, e = v, r(f, l - d, l)
            } else {
                var h = e.indexOf("<");
                if (0 === h) {
                    if (vs.test(e)) {
                        var m = e.indexOf("--\x3e");
                        if (m >= 0) {
                            n(m + 3);
                            continue
                        }
                    }
                    if (hs.test(e)) {
                        var g = e.indexOf("]>");
                        if (g >= 0) {
                            n(g + 2);
                            continue
                        }
                    }
                    var y = e.match(ds);
                    if (y) {
                        n(y[0].length);
                        continue
                    }
                    var _ = e.match(ps);
                    if (_) {
                        var b = l;
                        n(_[0].length), r(_[1], b, l);
                        continue
                    }
                    var $ = function () {
                        var t = e.match(ls);
                        if (t) {
                            var r = {tagName: t[1], attrs: [], start: l};
                            n(t[0].length);
                            for (var i, o; !(i = e.match(fs)) && (o = e.match(cs));) n(o[0].length), r.attrs.push(o);
                            if (i) return r.unarySlash = i[1], n(i[0].length), r.end = l, r
                        }
                    }();
                    if ($) {
                        !function (e) {
                            var n = e.tagName, i = e.unarySlash;
                            s && ("p" === o && as(n) && r(o), u(n) && o === n && r(n));
                            for (var l = c(n) || "html" === n && "head" === o || !!i, f = e.attrs.length, p = new Array(f), d = 0; d < f; d++) {
                                var v = e.attrs[d];
                                ms && -1 === v[0].indexOf('""') && ("" === v[3] && delete v[3], "" === v[4] && delete v[4], "" === v[5] && delete v[5]);
                                var h = v[3] || v[4] || v[5] || "";
                                p[d] = {name: v[1], value: mr(h, t.shouldDecodeNewlines)}
                            }
                            l || (a.push({
                                tag: n,
                                lowerCasedTag: n.toLowerCase(),
                                attrs: p
                            }), o = n), t.start && t.start(n, p, l, e.start, e.end)
                        }($);
                        continue
                    }
                }
                var C = void 0, x = void 0, w = void 0;
                if (h >= 0) {
                    for (x = e.slice(h); !(ps.test(x) || ls.test(x) || vs.test(x) || hs.test(x) || (w = x.indexOf("<", 1)) < 0);) h += w, x = e.slice(h);
                    C = e.substring(0, h), n(h)
                }
                h < 0 && (C = e, e = ""), t.chars && C && t.chars(C)
            }
            if (e === i) {
                t.chars && t.chars(e);
                break
            }
        }
        r()
    }

    function yr(e, t) {
        var n = t ? Hs(t) : Bs;
        if (n.test(e)) {
            for (var r, i, o = [], a = n.lastIndex = 0; r = n.exec(e);) {
                i = r.index, i > a && o.push(JSON.stringify(e.slice(a, i)));
                var s = rn(r[1].trim());
                o.push("_s(" + s + ")"), a = i + r[0].length
            }
            return a < e.length && o.push(JSON.stringify(e.slice(a))), o.join("+")
        }
    }

    function _r(e, t) {
        function n(e) {
            e.pre && (s = !1), Cs(e.tag) && (c = !1)
        }

        gs = t.warn || an, ws = t.getTagNamespace || Di, xs = t.mustUseProp || Di, Cs = t.isPreTag || Di, bs = sn(t.modules, "preTransformNode"), _s = sn(t.modules, "transformNode"), $s = sn(t.modules, "postTransformNode"), ys = t.delimiters;
        var r, i, o = [], a = !1 !== t.preserveWhitespace, s = !1, c = !1;
        return gr(e, {
            warn: gs,
            expectHTML: t.expectHTML,
            isUnaryTag: t.isUnaryTag,
            canBeLeftOpenTag: t.canBeLeftOpenTag,
            shouldDecodeNewlines: t.shouldDecodeNewlines,
            start: function (e, a, u) {
                var l = i && i.ns || ws(e);
                qi && "svg" === l && (a = Rr(a));
                var f = {type: 1, tag: e, attrsList: a, attrsMap: Dr(a), parent: i, children: []};
                l && (f.ns = l), Pr(f) && !ro() && (f.forbidden = !0);
                for (var p = 0; p < bs.length; p++) bs[p](f, t);
                if (s || (br(f), f.pre && (s = !0)), Cs(f.tag) && (c = !0), s) $r(f); else {
                    wr(f), kr(f), Tr(f), Cr(f), f.plain = !f.key && !a.length, xr(f), Er(f), jr(f);
                    for (var d = 0; d < _s.length; d++) _s[d](f, t);
                    Nr(f)
                }
                if (r ? o.length || r.if && (f.elseif || f.else) && Sr(r, {
                        exp: f.elseif,
                        block: f
                    }) : r = f, i && !f.forbidden) if (f.elseif || f.else) Ar(f, i); else if (f.slotScope) {
                    i.plain = !1;
                    var v = f.slotTarget || '"default"';
                    (i.scopedSlots || (i.scopedSlots = {}))[v] = f
                } else i.children.push(f), f.parent = i;
                u ? n(f) : (i = f, o.push(f));
                for (var h = 0; h < $s.length; h++) $s[h](f, t)
            },
            end: function () {
                var e = o[o.length - 1], t = e.children[e.children.length - 1];
                t && 3 === t.type && " " === t.text && !c && e.children.pop(), o.length -= 1, i = o[o.length - 1], n(e)
            },
            chars: function (e) {
                if (i && (!qi || "textarea" !== i.tag || i.attrsMap.placeholder !== e)) {
                    var t = i.children;
                    if (e = c || e.trim() ? Mr(i) ? e : Zs(e) : a && t.length ? " " : "") {
                        var n;
                        !s && " " !== e && (n = yr(e, ys)) ? t.push({
                            type: 2,
                            expression: n,
                            text: e
                        }) : " " === e && t.length && " " === t[t.length - 1].text || t.push({type: 3, text: e})
                    }
                }
            }
        }), r
    }

    function br(e) {
        null != dn(e, "v-pre") && (e.pre = !0)
    }

    function $r(e) {
        var t = e.attrsList.length;
        if (t) for (var n = e.attrs = new Array(t), r = 0; r < t; r++) n[r] = {
            name: e.attrsList[r].name,
            value: JSON.stringify(e.attrsList[r].value)
        }; else e.pre || (e.plain = !0)
    }

    function Cr(e) {
        var t = pn(e, "key");
        t && (e.key = t)
    }

    function xr(e) {
        var t = pn(e, "ref");
        t && (e.ref = t, e.refInFor = Lr(e))
    }

    function wr(e) {
        var t;
        if (t = dn(e, "v-for")) {
            var n = t.match(zs);
            if (!n) return;
            e.for = n[2].trim();
            var r = n[1].trim(), i = r.match(Js);
            i ? (e.alias = i[1].trim(), e.iterator1 = i[2].trim(), i[3] && (e.iterator2 = i[3].trim())) : e.alias = r
        }
    }

    function kr(e) {
        var t = dn(e, "v-if");
        if (t) e.if = t, Sr(e, {exp: t, block: e}); else {
            null != dn(e, "v-else") && (e.else = !0);
            var n = dn(e, "v-else-if");
            n && (e.elseif = n)
        }
    }

    function Ar(e, t) {
        var n = Or(t.children);
        n && n.if && Sr(n, {exp: e.elseif, block: e})
    }

    function Or(e) {
        for (var t = e.length; t--;) {
            if (1 === e[t].type) return e[t];
            e.pop()
        }
    }

    function Sr(e, t) {
        e.ifConditions || (e.ifConditions = []), e.ifConditions.push(t)
    }

    function Tr(e) {
        null != dn(e, "v-once") && (e.once = !0)
    }

    function Er(e) {
        if ("slot" === e.tag) e.slotName = pn(e, "name"); else {
            var t = pn(e, "slot");
            t && (e.slotTarget = '""' === t ? '"default"' : t), "template" === e.tag && (e.slotScope = dn(e, "scope"))
        }
    }

    function jr(e) {
        var t;
        (t = pn(e, "is")) && (e.component = t), null != dn(e, "inline-template") && (e.inlineTemplate = !0)
    }

    function Nr(e) {
        var t, n, r, i, o, a, s, c = e.attrsList;
        for (t = 0, n = c.length; t < n; t++) if (r = i = c[t].name, o = c[t].value, Vs.test(r)) if (e.hasBindings = !0, a = Ir(r), a && (r = r.replace(Ws, "")), qs.test(r)) r = r.replace(qs, ""), o = rn(o), s = !1, a && (a.prop && (s = !0, "innerHtml" === (r = Ni(r)) && (r = "innerHTML")), a.camel && (r = Ni(r)), a.sync && fn(e, "update:" + Ni(r), hn(o, "$event"))), s || xs(e.tag, e.attrsMap.type, r) ? cn(e, r, o) : un(e, r, o); else if (Us.test(r)) r = r.replace(Us, ""), fn(e, r, o, a, !1, gs); else {
            r = r.replace(Vs, "");
            var u = r.match(Ks), l = u && u[1];
            l && (r = r.slice(0, -(l.length + 1))), ln(e, r, i, o, l, a)
        } else un(e, r, JSON.stringify(o))
    }

    function Lr(e) {
        for (var t = e; t;) {
            if (void 0 !== t.for) return !0;
            t = t.parent
        }
        return !1
    }

    function Ir(e) {
        var t = e.match(Ws);
        if (t) {
            var n = {};
            return t.forEach(function (e) {
                n[e.slice(1)] = !0
            }), n
        }
    }

    function Dr(e) {
        for (var t = {}, n = 0, r = e.length; n < r; n++) t[e[n].name] = e[n].value;
        return t
    }

    function Mr(e) {
        return "script" === e.tag || "style" === e.tag
    }

    function Pr(e) {
        return "style" === e.tag || "script" === e.tag && (!e.attrsMap.type || "text/javascript" === e.attrsMap.type)
    }

    function Rr(e) {
        for (var t = [], n = 0; n < e.length; n++) {
            var r = e[n];
            Gs.test(r.name) || (r.name = r.name.replace(Ys, ""), t.push(r))
        }
        return t
    }

    function Fr(e, t) {
        e && (ks = Qs(t.staticKeys || ""), As = t.isReservedTag || Di, Hr(e), Ur(e, !1))
    }

    function Br(e) {
        return l("type,tag,attrsList,attrsMap,plain,parent,children,attrs" + (e ? "," + e : ""))
    }

    function Hr(e) {
        if (e.static = zr(e), 1 === e.type) {
            if (!As(e.tag) && "slot" !== e.tag && null == e.attrsMap["inline-template"]) return;
            for (var t = 0, n = e.children.length; t < n; t++) {
                var r = e.children[t];
                Hr(r), r.static || (e.static = !1)
            }
        }
    }

    function Ur(e, t) {
        if (1 === e.type) {
            if ((e.static || e.once) && (e.staticInFor = t), e.static && e.children.length && (1 !== e.children.length || 3 !== e.children[0].type)) return void(e.staticRoot = !0);
            if (e.staticRoot = !1, e.children) for (var n = 0, r = e.children.length; n < r; n++) Ur(e.children[n], t || !!e.for);
            e.ifConditions && Vr(e.ifConditions, t)
        }
    }

    function Vr(e, t) {
        for (var n = 1, r = e.length; n < r; n++) Ur(e[n].block, t)
    }

    function zr(e) {
        return 2 !== e.type && (3 === e.type || !(!e.pre && (e.hasBindings || e.if || e.for || Ei(e.tag) || !As(e.tag) || Jr(e) || !Object.keys(e).every(ks))))
    }

    function Jr(e) {
        for (; e.parent;) {
            if (e = e.parent, "template" !== e.tag) return !1;
            if (e.for) return !0
        }
        return !1
    }

    function Kr(e, t, n) {
        var r = t ? "nativeOn:{" : "on:{";
        for (var i in e) {
            var o = e[i];
            r += '"' + i + '":' + qr(i, o) + ","
        }
        return r.slice(0, -1) + "}"
    }

    function qr(e, t) {
        if (!t) return "function(){}";
        if (Array.isArray(t)) return "[" + t.map(function (t) {
            return qr(e, t)
        }).join(",") + "]";
        var n = ec.test(t.value), r = Xs.test(t.value);
        if (t.modifiers) {
            var i = "", o = "", a = [];
            for (var s in t.modifiers) rc[s] ? (o += rc[s], tc[s] && a.push(s)) : a.push(s);
            a.length && (i += Wr(a)), o && (i += o);
            return "function($event){" + i + (n ? t.value + "($event)" : r ? "(" + t.value + ")($event)" : t.value) + "}"
        }
        return n || r ? t.value : "function($event){" + t.value + "}"
    }

    function Wr(e) {
        return "if(!('button' in $event)&&" + e.map(Zr).join("&&") + ")return null;"
    }

    function Zr(e) {
        var t = parseInt(e, 10);
        if (t) return "$event.keyCode!==" + t;
        var n = tc[e];
        return "_k($event.keyCode," + JSON.stringify(e) + (n ? "," + JSON.stringify(n) : "") + ")"
    }

    function Gr(e, t) {
        e.wrapData = function (n) {
            return "_b(" + n + ",'" + e.tag + "'," + t.value + (t.modifiers && t.modifiers.prop ? ",true" : "") + ")"
        }
    }

    function Yr(e, t) {
        var n = Ns, r = Ns = [], i = Ls;
        Ls = 0, Is = t, Os = t.warn || an, Ss = sn(t.modules, "transformCode"), Ts = sn(t.modules, "genData"), Es = t.directives || {}, js = t.isReservedTag || Di;
        var o = e ? Qr(e) : '_c("div")';
        return Ns = n, Ls = i, {render: "with(this){return " + o + "}", staticRenderFns: r}
    }

    function Qr(e) {
        if (e.staticRoot && !e.staticProcessed) return Xr(e);
        if (e.once && !e.onceProcessed) return ei(e);
        if (e.for && !e.forProcessed) return ri(e);
        if (e.if && !e.ifProcessed) return ti(e);
        if ("template" !== e.tag || e.slotTarget) {
            if ("slot" === e.tag) return mi(e);
            var t;
            if (e.component) t = gi(e.component, e); else {
                var n = e.plain ? void 0 : ii(e), r = e.inlineTemplate ? null : li(e, !0);
                t = "_c('" + e.tag + "'" + (n ? "," + n : "") + (r ? "," + r : "") + ")"
            }
            for (var i = 0; i < Ss.length; i++) t = Ss[i](e, t);
            return t
        }
        return li(e) || "void 0"
    }

    function Xr(e) {
        return e.staticProcessed = !0, Ns.push("with(this){return " + Qr(e) + "}"), "_m(" + (Ns.length - 1) + (e.staticInFor ? ",true" : "") + ")"
    }

    function ei(e) {
        if (e.onceProcessed = !0, e.if && !e.ifProcessed) return ti(e);
        if (e.staticInFor) {
            for (var t = "", n = e.parent; n;) {
                if (n.for) {
                    t = n.key;
                    break
                }
                n = n.parent
            }
            return t ? "_o(" + Qr(e) + "," + Ls++ + (t ? "," + t : "") + ")" : Qr(e)
        }
        return Xr(e)
    }

    function ti(e) {
        return e.ifProcessed = !0, ni(e.ifConditions.slice())
    }

    function ni(e) {
        function t(e) {
            return e.once ? ei(e) : Qr(e)
        }

        if (!e.length) return "_e()";
        var n = e.shift();
        return n.exp ? "(" + n.exp + ")?" + t(n.block) + ":" + ni(e) : "" + t(n.block)
    }

    function ri(e) {
        var t = e.for, n = e.alias, r = e.iterator1 ? "," + e.iterator1 : "", i = e.iterator2 ? "," + e.iterator2 : "";
        return e.forProcessed = !0, "_l((" + t + "),function(" + n + r + i + "){return " + Qr(e) + "})"
    }

    function ii(e) {
        var t = "{", n = oi(e);
        n && (t += n + ","), e.key && (t += "key:" + e.key + ","), e.ref && (t += "ref:" + e.ref + ","), e.refInFor && (t += "refInFor:true,"), e.pre && (t += "pre:true,"), e.component && (t += 'tag:"' + e.tag + '",');
        for (var r = 0; r < Ts.length; r++) t += Ts[r](e);
        if (e.attrs && (t += "attrs:{" + yi(e.attrs) + "},"), e.props && (t += "domProps:{" + yi(e.props) + "},"), e.events && (t += Kr(e.events, !1, Os) + ","), e.nativeEvents && (t += Kr(e.nativeEvents, !0, Os) + ","), e.slotTarget && (t += "slot:" + e.slotTarget + ","), e.scopedSlots && (t += si(e.scopedSlots) + ","), e.model && (t += "model:{value:" + e.model.value + ",callback:" + e.model.callback + ",expression:" + e.model.expression + "},"), e.inlineTemplate) {
            var i = ai(e);
            i && (t += i + ",")
        }
        return t = t.replace(/,$/, "") + "}", e.wrapData && (t = e.wrapData(t)), t
    }

    function oi(e) {
        var t = e.directives;
        if (t) {
            var n, r, i, o, a = "directives:[", s = !1;
            for (n = 0, r = t.length; n < r; n++) {
                i = t[n], o = !0;
                var c = Es[i.name] || ic[i.name];
                c && (o = !!c(e, i, Os)), o && (s = !0, a += '{name:"' + i.name + '",rawName:"' + i.rawName + '"' + (i.value ? ",value:(" + i.value + "),expression:" + JSON.stringify(i.value) : "") + (i.arg ? ',arg:"' + i.arg + '"' : "") + (i.modifiers ? ",modifiers:" + JSON.stringify(i.modifiers) : "") + "},")
            }
            return s ? a.slice(0, -1) + "]" : void 0
        }
    }

    function ai(e) {
        var t = e.children[0];
        if (1 === t.type) {
            var n = Yr(t, Is);
            return "inlineTemplate:{render:function(){" + n.render + "},staticRenderFns:[" + n.staticRenderFns.map(function (e) {
                return "function(){" + e + "}"
            }).join(",") + "]}"
        }
    }

    function si(e) {
        return "scopedSlots:_u([" + Object.keys(e).map(function (t) {
            return ci(t, e[t])
        }).join(",") + "])"
    }

    function ci(e, t) {
        return t.for && !t.forProcessed ? ui(e, t) : "{key:" + e + ",fn:function(" + String(t.attrsMap.scope) + "){return " + ("template" === t.tag ? li(t) || "void 0" : Qr(t)) + "}}"
    }

    function ui(e, t) {
        var n = t.for, r = t.alias, i = t.iterator1 ? "," + t.iterator1 : "", o = t.iterator2 ? "," + t.iterator2 : "";
        return t.forProcessed = !0, "_l((" + n + "),function(" + r + i + o + "){return " + ci(e, t) + "})"
    }

    function li(e, t) {
        var n = e.children;
        if (n.length) {
            var r = n[0];
            if (1 === n.length && r.for && "template" !== r.tag && "slot" !== r.tag) return Qr(r);
            var i = t ? fi(n) : 0;
            return "[" + n.map(vi).join(",") + "]" + (i ? "," + i : "")
        }
    }

    function fi(e) {
        for (var t = 0, n = 0; n < e.length; n++) {
            var r = e[n];
            if (1 === r.type) {
                if (pi(r) || r.ifConditions && r.ifConditions.some(function (e) {
                        return pi(e.block)
                    })) {
                    t = 2;
                    break
                }
                (di(r) || r.ifConditions && r.ifConditions.some(function (e) {
                    return di(e.block)
                })) && (t = 1)
            }
        }
        return t
    }

    function pi(e) {
        return void 0 !== e.for || "template" === e.tag || "slot" === e.tag
    }

    function di(e) {
        return !js(e.tag)
    }

    function vi(e) {
        return 1 === e.type ? Qr(e) : hi(e)
    }

    function hi(e) {
        return "_v(" + (2 === e.type ? e.expression : _i(JSON.stringify(e.text))) + ")"
    }

    function mi(e) {
        var t = e.slotName || '"default"', n = li(e), r = "_t(" + t + (n ? "," + n : ""),
            i = e.attrs && "{" + e.attrs.map(function (e) {
                return Ni(e.name) + ":" + e.value
            }).join(",") + "}", o = e.attrsMap["v-bind"];
        return !i && !o || n || (r += ",null"), i && (r += "," + i), o && (r += (i ? "" : ",null") + "," + o), r + ")"
    }

    function gi(e, t) {
        var n = t.inlineTemplate ? null : li(t, !0);
        return "_c(" + e + "," + ii(t) + (n ? "," + n : "") + ")"
    }

    function yi(e) {
        for (var t = "", n = 0; n < e.length; n++) {
            var r = e[n];
            t += '"' + r.name + '":' + _i(r.value) + ","
        }
        return t.slice(0, -1)
    }

    function _i(e) {
        return e.replace(/\u2028/g, "\\u2028").replace(/\u2029/g, "\\u2029")
    }

    function bi(e, t) {
        var n = _r(e.trim(), t);
        Fr(n, t);
        var r = Yr(n, t);
        return {ast: n, render: r.render, staticRenderFns: r.staticRenderFns}
    }

    function $i(e, t) {
        try {
            return new Function(e)
        } catch (n) {
            return t.push({err: n, code: e}), y
        }
    }

    function Ci(e, t) {
        var n = (t.warn, dn(e, "class"));
        n && (e.staticClass = JSON.stringify(n));
        var r = pn(e, "class", !1);
        r && (e.classBinding = r)
    }

    function xi(e) {
        var t = "";
        return e.staticClass && (t += "staticClass:" + e.staticClass + ","), e.classBinding && (t += "class:" + e.classBinding + ","), t
    }

    function wi(e, t) {
        var n = (t.warn, dn(e, "style"));
        n && (e.staticStyle = JSON.stringify(Oa(n)));
        var r = pn(e, "style", !1);
        r && (e.styleBinding = r)
    }

    function ki(e) {
        var t = "";
        return e.staticStyle && (t += "staticStyle:" + e.staticStyle + ","), e.styleBinding && (t += "style:(" + e.styleBinding + "),"), t
    }

    function Ai(e, t) {
        t.value && cn(e, "textContent", "_s(" + t.value + ")")
    }

    function Oi(e, t) {
        t.value && cn(e, "innerHTML", "_s(" + t.value + ")")
    }

    function Si(e) {
        if (e.outerHTML) return e.outerHTML;
        var t = document.createElement("div");
        return t.appendChild(e.cloneNode(!0)), t.innerHTML
    }

    var Ti = Object.prototype.toString, Ei = l("slot,component", !0), ji = Object.prototype.hasOwnProperty,
        Ni = d(function (e) {
            return e.replace(/-(\w)/g, function (e, t) {
                return t ? t.toUpperCase() : ""
            })
        }), Li = d(function (e) {
            return e.charAt(0).toUpperCase() + e.slice(1)
        }), Ii = d(function (e) {
            return e.replace(/([^-])([A-Z])/g, "$1-$2").replace(/([^-])([A-Z])/g, "$1-$2").toLowerCase()
        }), Di = function () {
            return !1
        }, Mi = function (e) {
            return e
        }, Pi = "data-server-rendered", Ri = ["component", "directive", "filter"],
        Fi = ["beforeCreate", "created", "beforeMount", "mounted", "beforeUpdate", "updated", "beforeDestroy", "destroyed", "activated", "deactivated"],
        Bi = {
            optionMergeStrategies: Object.create(null),
            silent: !1,
            productionTip: !1,
            devtools: !1,
            performance: !1,
            errorHandler: null,
            ignoredElements: [],
            keyCodes: Object.create(null),
            isReservedTag: Di,
            isReservedAttr: Di,
            isUnknownElement: Di,
            getTagNamespace: y,
            parsePlatformTagName: Mi,
            mustUseProp: Di,
            _lifecycleHooks: Fi
        }, Hi = Object.freeze({}), Ui = /[^\w.$]/, Vi = y, zi = "__proto__" in {}, Ji = "undefined" != typeof window,
        Ki = Ji && window.navigator.userAgent.toLowerCase(), qi = Ki && /msie|trident/.test(Ki),
        Wi = Ki && Ki.indexOf("msie 9.0") > 0, Zi = Ki && Ki.indexOf("edge/") > 0, Gi = Ki && Ki.indexOf("android") > 0,
        Yi = Ki && /iphone|ipad|ipod|ios/.test(Ki), Qi = Ki && /chrome\/\d+/.test(Ki) && !Zi, Xi = !1;
    if (Ji) try {
        var eo = {};
        Object.defineProperty(eo, "passive", {
            get: function () {
                Xi = !0
            }
        }), window.addEventListener("test-passive", null, eo)
    } catch (e) {
    }
    var to, no, ro = function () {
            return void 0 === to && (to = !Ji && "undefined" != typeof global && "server" === global.process.env.VUE_ENV), to
        }, io = Ji && window.__VUE_DEVTOOLS_GLOBAL_HOOK__,
        oo = "undefined" != typeof Symbol && A(Symbol) && "undefined" != typeof Reflect && A(Reflect.ownKeys),
        ao = function () {
            function e() {
                r = !1;
                var e = n.slice(0);
                n.length = 0;
                for (var t = 0; t < e.length; t++) e[t]()
            }

            var t, n = [], r = !1;
            if ("undefined" != typeof Promise && A(Promise)) {
                var i = Promise.resolve(), o = function (e) {
                    console.error(e)
                };
                t = function () {
                    i.then(e).catch(o), Yi && setTimeout(y)
                }
            } else if ("undefined" == typeof MutationObserver || !A(MutationObserver) && "[object MutationObserverConstructor]" !== MutationObserver.toString()) t = function () {
                setTimeout(e, 0)
            }; else {
                var a = 1, s = new MutationObserver(e), c = document.createTextNode(String(a));
                s.observe(c, {characterData: !0}), t = function () {
                    a = (a + 1) % 2, c.data = String(a)
                }
            }
            return function (e, i) {
                var o;
                if (n.push(function () {
                        if (e) try {
                            e.call(i)
                        } catch (e) {
                            k(e, i, "nextTick")
                        } else o && o(i)
                    }), r || (r = !0, t()), !e && "undefined" != typeof Promise) return new Promise(function (e, t) {
                    o = e
                })
            }
        }();
    no = "undefined" != typeof Set && A(Set) ? Set : function () {
        function e() {
            this.set = Object.create(null)
        }

        return e.prototype.has = function (e) {
            return !0 === this.set[e]
        }, e.prototype.add = function (e) {
            this.set[e] = !0
        }, e.prototype.clear = function () {
            this.set = Object.create(null)
        }, e
    }();
    var so = 0, co = function () {
        this.id = so++, this.subs = []
    };
    co.prototype.addSub = function (e) {
        this.subs.push(e)
    }, co.prototype.removeSub = function (e) {
        f(this.subs, e)
    }, co.prototype.depend = function () {
        co.target && co.target.addDep(this)
    }, co.prototype.notify = function () {
        for (var e = this.subs.slice(), t = 0, n = e.length; t < n; t++) e[t].update()
    }, co.target = null;
    var uo = [], lo = Array.prototype, fo = Object.create(lo);
    ["push", "pop", "shift", "unshift", "splice", "sort", "reverse"].forEach(function (e) {
        var t = lo[e];
        x(fo, e, function () {
            for (var n = arguments, r = arguments.length, i = new Array(r); r--;) i[r] = n[r];
            var o, a = t.apply(this, i), s = this.__ob__;
            switch (e) {
                case"push":
                case"unshift":
                    o = i;
                    break;
                case"splice":
                    o = i.slice(2)
            }
            return o && s.observeArray(o), s.dep.notify(), a
        })
    });
    var po = Object.getOwnPropertyNames(fo), vo = {shouldConvert: !0, isSettingProps: !1}, ho = function (e) {
        if (this.value = e, this.dep = new co, this.vmCount = 0, x(e, "__ob__", this), Array.isArray(e)) {
            (zi ? T : E)(e, fo, po), this.observeArray(e)
        } else this.walk(e)
    };
    ho.prototype.walk = function (e) {
        for (var t = Object.keys(e), n = 0; n < t.length; n++) N(e, t[n], e[t[n]])
    }, ho.prototype.observeArray = function (e) {
        for (var t = 0, n = e.length; t < n; t++) j(e[t])
    };
    var mo = Bi.optionMergeStrategies;
    mo.data = function (e, t, n) {
        return n ? e || t ? function () {
            var r = "function" == typeof t ? t.call(n) : t, i = "function" == typeof e ? e.call(n) : void 0;
            return r ? M(r, i) : i
        } : void 0 : t ? "function" != typeof t ? e : e ? function () {
            return M(t.call(this), e.call(this))
        } : t : e
    }, Fi.forEach(function (e) {
        mo[e] = P
    }), Ri.forEach(function (e) {
        mo[e + "s"] = R
    }), mo.watch = function (e, t) {
        if (!t) return Object.create(e || null);
        if (!e) return t;
        var n = {};
        m(n, e);
        for (var r in t) {
            var i = n[r], o = t[r];
            i && !Array.isArray(i) && (i = [i]), n[r] = i ? i.concat(o) : [o]
        }
        return n
    }, mo.props = mo.methods = mo.computed = function (e, t) {
        if (!t) return Object.create(e || null);
        if (!e) return t;
        var n = Object.create(null);
        return m(n, e), m(n, t), n
    };
    var go = function (e, t) {
        return void 0 === t ? e : t
    }, yo = function (e, t, n, r, i, o, a) {
        this.tag = e, this.data = t, this.children = n, this.text = r, this.elm = i, this.ns = void 0, this.context = o, this.functionalContext = void 0, this.key = t && t.key, this.componentOptions = a, this.componentInstance = void 0, this.parent = void 0, this.raw = !1, this.isStatic = !1, this.isRootInsert = !0, this.isComment = !1, this.isCloned = !1, this.isOnce = !1
    }, _o = {child: {}};
    _o.child.get = function () {
        return this.componentInstance
    }, Object.defineProperties(yo.prototype, _o);
    var bo, $o = function () {
        var e = new yo;
        return e.text = "", e.isComment = !0, e
    }, Co = d(function (e) {
        var t = "&" === e.charAt(0);
        e = t ? e.slice(1) : e;
        var n = "~" === e.charAt(0);
        e = n ? e.slice(1) : e;
        var r = "!" === e.charAt(0);
        return e = r ? e.slice(1) : e, {name: e, once: n, capture: r, passive: t}
    }), xo = null, wo = [], ko = [], Ao = {}, Oo = !1, So = !1, To = 0, Eo = 0, jo = function (e, t, n, r) {
        this.vm = e, e._watchers.push(this), r ? (this.deep = !!r.deep, this.user = !!r.user, this.lazy = !!r.lazy, this.sync = !!r.sync) : this.deep = this.user = this.lazy = this.sync = !1, this.cb = n, this.id = ++Eo, this.active = !0, this.dirty = this.lazy, this.deps = [], this.newDeps = [], this.depIds = new no, this.newDepIds = new no, this.expression = "", "function" == typeof t ? this.getter = t : (this.getter = w(t), this.getter || (this.getter = function () {
        })), this.value = this.lazy ? void 0 : this.get()
    };
    jo.prototype.get = function () {
        O(this);
        var e, t = this.vm;
        if (this.user) try {
            e = this.getter.call(t, t)
        } catch (e) {
            k(e, t, 'getter for watcher "' + this.expression + '"')
        } else e = this.getter.call(t, t);
        return this.deep && Se(e), S(), this.cleanupDeps(), e
    }, jo.prototype.addDep = function (e) {
        var t = e.id;
        this.newDepIds.has(t) || (this.newDepIds.add(t), this.newDeps.push(e), this.depIds.has(t) || e.addSub(this))
    }, jo.prototype.cleanupDeps = function () {
        for (var e = this, t = this.deps.length; t--;) {
            var n = e.deps[t];
            e.newDepIds.has(n.id) || n.removeSub(e)
        }
        var r = this.depIds;
        this.depIds = this.newDepIds, this.newDepIds = r, this.newDepIds.clear(), r = this.deps, this.deps = this.newDeps, this.newDeps = r, this.newDeps.length = 0
    }, jo.prototype.update = function () {
        this.lazy ? this.dirty = !0 : this.sync ? this.run() : Oe(this)
    }, jo.prototype.run = function () {
        if (this.active) {
            var e = this.get();
            if (e !== this.value || o(e) || this.deep) {
                var t = this.value;
                if (this.value = e, this.user) try {
                    this.cb.call(this.vm, e, t)
                } catch (e) {
                    k(e, this.vm, 'callback for watcher "' + this.expression + '"')
                } else this.cb.call(this.vm, e, t)
            }
        }
    }, jo.prototype.evaluate = function () {
        this.value = this.get(), this.dirty = !1
    }, jo.prototype.depend = function () {
        for (var e = this, t = this.deps.length; t--;) e.deps[t].depend()
    }, jo.prototype.teardown = function () {
        var e = this;
        if (this.active) {
            this.vm._isBeingDestroyed || f(this.vm._watchers, this);
            for (var t = this.deps.length; t--;) e.deps[t].removeSub(e);
            this.active = !1
        }
    };
    var No = new no, Lo = {enumerable: !0, configurable: !0, get: y, set: y}, Io = {lazy: !0}, Do = {
        init: function (e, t, n, r) {
            if (!e.componentInstance || e.componentInstance._isDestroyed) {
                (e.componentInstance = qe(e, xo, n, r)).$mount(t ? e.elm : void 0, t)
            } else if (e.data.keepAlive) {
                var i = e;
                Do.prepatch(i, i)
            }
        }, prepatch: function (e, t) {
            var n = t.componentOptions;
            ge(t.componentInstance = e.componentInstance, n.propsData, n.listeners, t, n.children)
        }, insert: function (e) {
            var t = e.context, n = e.componentInstance;
            n._isMounted || (n._isMounted = !0, $e(n, "mounted")), e.data.keepAlive && (t._isMounted ? ke(n) : _e(n, !0))
        }, destroy: function (e) {
            var t = e.componentInstance;
            t._isDestroyed || (e.data.keepAlive ? be(t, !0) : t.$destroy())
        }
    }, Mo = Object.keys(Do), Po = 1, Ro = 2, Fo = 0;
    !function (e) {
        e.prototype._init = function (e) {
            var t = this;
            t._uid = Fo++, t._isVue = !0, e && e._isComponent ? lt(t, e) : t.$options = H(ft(t.constructor), e || {}, t), t._renderProxy = t, t._self = t, he(t), ce(t), ut(t), $e(t, "beforeCreate"), Ue(t), je(t), He(t), $e(t, "created"), t.$options.el && t.$mount(t.$options.el)
        }
    }(vt), function (e) {
        var t = {};
        t.get = function () {
            return this._data
        };
        var n = {};
        n.get = function () {
            return this._props
        }, Object.defineProperty(e.prototype, "$data", t), Object.defineProperty(e.prototype, "$props", n), e.prototype.$set = L, e.prototype.$delete = I, e.prototype.$watch = function (e, t, n) {
            var r = this;
            n = n || {}, n.user = !0;
            var i = new jo(r, e, t, n);
            return n.immediate && t.call(r, i.value), function () {
                i.teardown()
            }
        }
    }(vt), function (e) {
        var t = /^hook:/;
        e.prototype.$on = function (e, n) {
            var r = this, i = this;
            if (Array.isArray(e)) for (var o = 0, a = e.length; o < a; o++) r.$on(e[o], n); else (i._events[e] || (i._events[e] = [])).push(n), t.test(e) && (i._hasHookEvent = !0);
            return i
        }, e.prototype.$once = function (e, t) {
            function n() {
                r.$off(e, n), t.apply(r, arguments)
            }

            var r = this;
            return n.fn = t, r.$on(e, n), r
        }, e.prototype.$off = function (e, t) {
            var n = this, r = this;
            if (!arguments.length) return r._events = Object.create(null), r;
            if (Array.isArray(e)) {
                for (var i = 0, o = e.length; i < o; i++) n.$off(e[i], t);
                return r
            }
            var a = r._events[e];
            if (!a) return r;
            if (1 === arguments.length) return r._events[e] = null, r;
            for (var s, c = a.length; c--;) if ((s = a[c]) === t || s.fn === t) {
                a.splice(c, 1);
                break
            }
            return r
        }, e.prototype.$emit = function (e) {
            var t = this, n = t._events[e];
            if (n) {
                n = n.length > 1 ? h(n) : n;
                for (var r = h(arguments, 1), i = 0, o = n.length; i < o; i++) n[i].apply(t, r)
            }
            return t
        }
    }(vt), function (e) {
        e.prototype._update = function (e, t) {
            var n = this;
            n._isMounted && $e(n, "beforeUpdate");
            var r = n.$el, i = n._vnode, o = xo;
            xo = n, n._vnode = e, n.$el = i ? n.__patch__(i, e) : n.__patch__(n.$el, e, t, !1, n.$options._parentElm, n.$options._refElm), xo = o, r && (r.__vue__ = null), n.$el && (n.$el.__vue__ = n), n.$vnode && n.$parent && n.$vnode === n.$parent._vnode && (n.$parent.$el = n.$el)
        }, e.prototype.$forceUpdate = function () {
            var e = this;
            e._watcher && e._watcher.update()
        }, e.prototype.$destroy = function () {
            var e = this;
            if (!e._isBeingDestroyed) {
                $e(e, "beforeDestroy"), e._isBeingDestroyed = !0;
                var t = e.$parent;
                !t || t._isBeingDestroyed || e.$options.abstract || f(t.$children, e), e._watcher && e._watcher.teardown();
                for (var n = e._watchers.length; n--;) e._watchers[n].teardown();
                e._data.__ob__ && e._data.__ob__.vmCount--, e._isDestroyed = !0, e.__patch__(e._vnode, null), $e(e, "destroyed"), e.$off(), e.$el && (e.$el.__vue__ = null), e.$options._parentElm = e.$options._refElm = null
            }
        }
    }(vt), function (e) {
        e.prototype.$nextTick = function (e) {
            return ao(e, this)
        }, e.prototype._render = function () {
            var e = this, t = e.$options, n = t.render, r = t.staticRenderFns, i = t._parentVnode;
            if (e._isMounted) for (var o in e.$slots) e.$slots[o] = Z(e.$slots[o]);
            e.$scopedSlots = i && i.data.scopedSlots || Hi, r && !e._staticTrees && (e._staticTrees = []), e.$vnode = i;
            var a;
            try {
                a = n.call(e._renderProxy, e.$createElement)
            } catch (t) {
                k(t, e, "render function"), a = e._vnode
            }
            return a instanceof yo || (a = $o()), a.parent = i, a
        }, e.prototype._o = at, e.prototype._n = u, e.prototype._s = c, e.prototype._l = et, e.prototype._t = tt, e.prototype._q = _, e.prototype._i = b, e.prototype._m = ot, e.prototype._f = nt, e.prototype._k = rt, e.prototype._b = it, e.prototype._v = q, e.prototype._e = $o, e.prototype._u = ve
    }(vt);
    var Bo = [String, RegExp], Ho = {
        name: "keep-alive", abstract: !0, props: {include: Bo, exclude: Bo}, created: function () {
            this.cache = Object.create(null)
        }, destroyed: function () {
            var e = this;
            for (var t in e.cache) wt(e.cache[t])
        }, watch: {
            include: function (e) {
                xt(this.cache, this._vnode, function (t) {
                    return Ct(e, t)
                })
            }, exclude: function (e) {
                xt(this.cache, this._vnode, function (t) {
                    return !Ct(e, t)
                })
            }
        }, render: function () {
            var e = se(this.$slots.default), t = e && e.componentOptions;
            if (t) {
                var n = $t(t);
                if (n && (this.include && !Ct(this.include, n) || this.exclude && Ct(this.exclude, n))) return e;
                var r = null == e.key ? t.Ctor.cid + (t.tag ? "::" + t.tag : "") : e.key;
                this.cache[r] ? e.componentInstance = this.cache[r].componentInstance : this.cache[r] = e, e.data.keepAlive = !0
            }
            return e
        }
    }, Uo = {KeepAlive: Ho};
    !function (e) {
        var t = {};
        t.get = function () {
            return Bi
        }, Object.defineProperty(e, "config", t), e.util = {
            warn: Vi,
            extend: m,
            mergeOptions: H,
            defineReactive: N
        }, e.set = L, e.delete = I, e.nextTick = ao, e.options = Object.create(null), Ri.forEach(function (t) {
            e.options[t + "s"] = Object.create(null)
        }), e.options._base = e, m(e.options.components, Uo), ht(e), mt(e), gt(e), bt(e)
    }(vt), Object.defineProperty(vt.prototype, "$isServer", {get: ro}), Object.defineProperty(vt.prototype, "$ssrContext", {
        get: function () {
            return this.$vnode.ssrContext
        }
    }), vt.version = "2.3.3";
    var Vo, zo, Jo, Ko, qo, Wo, Zo, Go, Yo, Qo = l("style,class"), Xo = l("input,textarea,option,select"),
        ea = function (e, t, n) {
            return "value" === n && Xo(e) && "button" !== t || "selected" === n && "option" === e || "checked" === n && "input" === e || "muted" === n && "video" === e
        }, ta = l("contenteditable,draggable,spellcheck"),
        na = l("allowfullscreen,async,autofocus,autoplay,checked,compact,controls,declare,default,defaultchecked,defaultmuted,defaultselected,defer,disabled,enabled,formnovalidate,hidden,indeterminate,inert,ismap,itemscope,loop,multiple,muted,nohref,noresize,noshade,novalidate,nowrap,open,pauseonexit,readonly,required,reversed,scoped,seamless,selected,sortable,translate,truespeed,typemustmatch,visible"),
        ra = "http://www.w3.org/1999/xlink", ia = function (e) {
            return ":" === e.charAt(5) && "xlink" === e.slice(0, 5)
        }, oa = function (e) {
            return ia(e) ? e.slice(6, e.length) : ""
        }, aa = function (e) {
            return null == e || !1 === e
        }, sa = {svg: "http://www.w3.org/2000/svg", math: "http://www.w3.org/1998/Math/MathML"},
        ca = l("html,body,base,head,link,meta,style,title,address,article,aside,footer,header,h1,h2,h3,h4,h5,h6,hgroup,nav,section,div,dd,dl,dt,figcaption,figure,hr,img,li,main,ol,p,pre,ul,a,b,abbr,bdi,bdo,br,cite,code,data,dfn,em,i,kbd,mark,q,rp,rt,rtc,ruby,s,samp,small,span,strong,sub,sup,time,u,var,wbr,area,audio,map,track,video,embed,object,param,source,canvas,script,noscript,del,ins,caption,col,colgroup,table,thead,tbody,td,th,tr,button,datalist,fieldset,form,input,label,legend,meter,optgroup,option,output,progress,select,textarea,details,dialog,menu,menuitem,summary,content,element,shadow,template"),
        ua = l("svg,animate,circle,clippath,cursor,defs,desc,ellipse,filter,font-face,foreignObject,g,glyph,image,line,marker,mask,missing-glyph,path,pattern,polygon,polyline,rect,switch,symbol,text,textpath,tspan,use,view", !0),
        la = function (e) {
            return "pre" === e
        }, fa = function (e) {
            return ca(e) || ua(e)
        }, pa = Object.create(null), da = Object.freeze({
            createElement: Lt,
            createElementNS: It,
            createTextNode: Dt,
            createComment: Mt,
            insertBefore: Pt,
            removeChild: Rt,
            appendChild: Ft,
            parentNode: Bt,
            nextSibling: Ht,
            tagName: Ut,
            setTextContent: Vt,
            setAttribute: zt
        }), va = {
            create: function (e, t) {
                Jt(t)
            }, update: function (e, t) {
                e.data.ref !== t.data.ref && (Jt(e, !0), Jt(t))
            }, destroy: function (e) {
                Jt(e, !0)
            }
        }, ha = new yo("", {}, []), ma = ["create", "activate", "update", "remove", "destroy"], ga = {
            create: Zt, update: Zt, destroy: function (e) {
                Zt(e, ha)
            }
        }, ya = Object.create(null), _a = [va, ga], ba = {create: en, update: en}, $a = {create: nn, update: nn},
        Ca = /[\w).+\-_$\]]/, xa = "__r", wa = "__c", ka = {create: En, update: En}, Aa = {create: jn, update: jn},
        Oa = d(function (e) {
            var t = {};
            return e.split(/;(?![^(]*\))/g).forEach(function (e) {
                if (e) {
                    var n = e.split(/:(.+)/);
                    n.length > 1 && (t[n[0].trim()] = n[1].trim())
                }
            }), t
        }), Sa = /^--/, Ta = /\s*!important$/, Ea = function (e, t, n) {
            if (Sa.test(t)) e.style.setProperty(t, n); else if (Ta.test(n)) e.style.setProperty(t, n.replace(Ta, ""), "important"); else {
                var r = Na(t);
                if (Array.isArray(n)) for (var i = 0, o = n.length; i < o; i++) e.style[r] = n[i]; else e.style[r] = n
            }
        }, ja = ["Webkit", "Moz", "ms"], Na = d(function (e) {
            if (Yo = Yo || document.createElement("div"), "filter" !== (e = Ni(e)) && e in Yo.style) return e;
            for (var t = e.charAt(0).toUpperCase() + e.slice(1), n = 0; n < ja.length; n++) {
                var r = ja[n] + t;
                if (r in Yo.style) return r
            }
        }), La = {create: Rn, update: Rn}, Ia = d(function (e) {
            return {
                enterClass: e + "-enter",
                enterToClass: e + "-enter-to",
                enterActiveClass: e + "-enter-active",
                leaveClass: e + "-leave",
                leaveToClass: e + "-leave-to",
                leaveActiveClass: e + "-leave-active"
            }
        }), Da = Ji && !Wi, Ma = "transition", Pa = "animation", Ra = "transition", Fa = "transitionend", Ba = "animation",
        Ha = "animationend";
    Da && (void 0 === window.ontransitionend && void 0 !== window.onwebkittransitionend && (Ra = "WebkitTransition", Fa = "webkitTransitionEnd"), void 0 === window.onanimationend && void 0 !== window.onwebkitanimationend && (Ba = "WebkitAnimation", Ha = "webkitAnimationEnd"));
    var Ua = Ji && window.requestAnimationFrame ? window.requestAnimationFrame.bind(window) : setTimeout,
        Va = /\b(transform|all)(,|$)/, za = Ji ? {
            create: Xn, activate: Xn, remove: function (e, t) {
                !0 !== e.data.show ? Gn(e, t) : t()
            }
        } : {}, Ja = [ba, $a, ka, Aa, La, za], Ka = Ja.concat(_a), qa = function (r) {
            function o(e) {
                return new yo(E.tagName(e).toLowerCase(), {}, [], void 0, e)
            }

            function a(e, t) {
                function n() {
                    0 == --n.listeners && s(e)
                }

                return n.listeners = t, n
            }

            function s(e) {
                var n = E.parentNode(e);
                t(n) && E.removeChild(n, e)
            }

            function c(e, r, i, o, a) {
                if (e.isRootInsert = !a, !u(e, r, i, o)) {
                    var s = e.data, c = e.children, l = e.tag;
                    t(l) ? (e.elm = e.ns ? E.createElementNS(e.ns, l) : E.createElement(l, e), g(e), v(e, c, r), t(s) && m(e, r), d(i, e.elm, o)) : n(e.isComment) ? (e.elm = E.createComment(e.text), d(i, e.elm, o)) : (e.elm = E.createTextNode(e.text), d(i, e.elm, o))
                }
            }

            function u(e, r, i, o) {
                var a = e.data;
                if (t(a)) {
                    var s = t(e.componentInstance) && a.keepAlive;
                    if (t(a = a.hook) && t(a = a.init) && a(e, !1, i, o), t(e.componentInstance)) return f(e, r), n(s) && p(e, r, i, o), !0
                }
            }

            function f(e, n) {
                t(e.data.pendingInsert) && n.push.apply(n, e.data.pendingInsert), e.elm = e.componentInstance.$el, h(e) ? (m(e, n), g(e)) : (Jt(e), n.push(e))
            }

            function p(e, n, r, i) {
                for (var o, a = e; a.componentInstance;) if (a = a.componentInstance._vnode, t(o = a.data) && t(o = o.transition)) {
                    for (o = 0; o < S.activate.length; ++o) S.activate[o](ha, a);
                    n.push(a);
                    break
                }
                d(r, e.elm, i)
            }

            function d(e, n, r) {
                t(e) && (t(r) ? r.parentNode === e && E.insertBefore(e, n, r) : E.appendChild(e, n))
            }

            function v(e, t, n) {
                if (Array.isArray(t)) for (var r = 0; r < t.length; ++r) c(t[r], n, e.elm, null, !0); else i(e.text) && E.appendChild(e.elm, E.createTextNode(e.text))
            }

            function h(e) {
                for (; e.componentInstance;) e = e.componentInstance._vnode;
                return t(e.tag)
            }

            function m(e, n) {
                for (var r = 0; r < S.create.length; ++r) S.create[r](ha, e);
                A = e.data.hook, t(A) && (t(A.create) && A.create(ha, e), t(A.insert) && n.push(e))
            }

            function g(e) {
                for (var n, r = e; r;) t(n = r.context) && t(n = n.$options._scopeId) && E.setAttribute(e.elm, n, ""), r = r.parent;
                t(n = xo) && n !== e.context && t(n = n.$options._scopeId) && E.setAttribute(e.elm, n, "")
            }

            function y(e, t, n, r, i, o) {
                for (; r <= i; ++r) c(n[r], o, e, t)
            }

            function _(e) {
                var n, r, i = e.data;
                if (t(i)) for (t(n = i.hook) && t(n = n.destroy) && n(e), n = 0; n < S.destroy.length; ++n) S.destroy[n](e);
                if (t(n = e.children)) for (r = 0; r < e.children.length; ++r) _(e.children[r])
            }

            function b(e, n, r, i) {
                for (; r <= i; ++r) {
                    var o = n[r];
                    t(o) && (t(o.tag) ? ($(o), _(o)) : s(o.elm))
                }
            }

            function $(e, n) {
                if (t(n) || t(e.data)) {
                    var r, i = S.remove.length + 1;
                    for (t(n) ? n.listeners += i : n = a(e.elm, i), t(r = e.componentInstance) && t(r = r._vnode) && t(r.data) && $(r, n), r = 0; r < S.remove.length; ++r) S.remove[r](e, n);
                    t(r = e.data.hook) && t(r = r.remove) ? r(e, n) : n()
                } else s(e.elm)
            }

            function C(n, r, i, o, a) {
                for (var s, u, l, f, p = 0, d = 0, v = r.length - 1, h = r[0], m = r[v], g = i.length - 1, _ = i[0], $ = i[g], C = !a; p <= v && d <= g;) e(h) ? h = r[++p] : e(m) ? m = r[--v] : Kt(h, _) ? (x(h, _, o), h = r[++p], _ = i[++d]) : Kt(m, $) ? (x(m, $, o), m = r[--v], $ = i[--g]) : Kt(h, $) ? (x(h, $, o), C && E.insertBefore(n, h.elm, E.nextSibling(m.elm)), h = r[++p], $ = i[--g]) : Kt(m, _) ? (x(m, _, o), C && E.insertBefore(n, m.elm, h.elm), m = r[--v], _ = i[++d]) : (e(s) && (s = Wt(r, p, v)), u = t(_.key) ? s[_.key] : null, e(u) ? (c(_, o, n, h.elm), _ = i[++d]) : (l = r[u], Kt(l, _) ? (x(l, _, o), r[u] = void 0, C && E.insertBefore(n, _.elm, h.elm), _ = i[++d]) : (c(_, o, n, h.elm), _ = i[++d])));
                p > v ? (f = e(i[g + 1]) ? null : i[g + 1].elm, y(n, f, i, d, g, o)) : d > g && b(n, r, p, v)
            }

            function x(r, i, o, a) {
                if (r !== i) {
                    if (n(i.isStatic) && n(r.isStatic) && i.key === r.key && (n(i.isCloned) || n(i.isOnce))) return i.elm = r.elm, void(i.componentInstance = r.componentInstance);
                    var s, c = i.data;
                    t(c) && t(s = c.hook) && t(s = s.prepatch) && s(r, i);
                    var u = i.elm = r.elm, l = r.children, f = i.children;
                    if (t(c) && h(i)) {
                        for (s = 0; s < S.update.length; ++s) S.update[s](r, i);
                        t(s = c.hook) && t(s = s.update) && s(r, i)
                    }
                    e(i.text) ? t(l) && t(f) ? l !== f && C(u, l, f, o, a) : t(f) ? (t(r.text) && E.setTextContent(u, ""), y(u, null, f, 0, f.length - 1, o)) : t(l) ? b(u, l, 0, l.length - 1) : t(r.text) && E.setTextContent(u, "") : r.text !== i.text && E.setTextContent(u, i.text), t(c) && t(s = c.hook) && t(s = s.postpatch) && s(r, i)
                }
            }

            function w(e, r, i) {
                if (n(i) && t(e.parent)) e.parent.data.pendingInsert = r; else for (var o = 0; o < r.length; ++o) r[o].data.hook.insert(r[o])
            }

            function k(e, n, r) {
                n.elm = e;
                var i = n.tag, o = n.data, a = n.children;
                if (t(o) && (t(A = o.hook) && t(A = A.init) && A(n, !0), t(A = n.componentInstance))) return f(n, r), !0;
                if (t(i)) {
                    if (t(a)) if (e.hasChildNodes()) {
                        for (var s = !0, c = e.firstChild, u = 0; u < a.length; u++) {
                            if (!c || !k(c, a[u], r)) {
                                s = !1;
                                break
                            }
                            c = c.nextSibling
                        }
                        if (!s || c) return !1
                    } else v(n, a, r);
                    if (t(o)) for (var l in o) if (!j(l)) {
                        m(n, r);
                        break
                    }
                } else e.data !== n.text && (e.data = n.text);
                return !0
            }

            var A, O, S = {}, T = r.modules, E = r.nodeOps;
            for (A = 0; A < ma.length; ++A) for (S[ma[A]] = [], O = 0; O < T.length; ++O) t(T[O][ma[A]]) && S[ma[A]].push(T[O][ma[A]]);
            var j = l("attrs,style,class,staticClass,staticStyle,key");
            return function (r, i, a, s, u, l) {
                if (e(i)) return void(t(r) && _(r));
                var f = !1, p = [];
                if (e(r)) f = !0, c(i, p, u, l); else {
                    var d = t(r.nodeType);
                    if (!d && Kt(r, i)) x(r, i, p, s); else {
                        if (d) {
                            if (1 === r.nodeType && r.hasAttribute(Pi) && (r.removeAttribute(Pi), a = !0), n(a) && k(r, i, p)) return w(i, p, !0), r;
                            r = o(r)
                        }
                        var v = r.elm, m = E.parentNode(v);
                        if (c(i, p, v._leaveCb ? null : m, E.nextSibling(v)), t(i.parent)) {
                            for (var g = i.parent; g;) g.elm = i.elm, g = g.parent;
                            if (h(i)) for (var y = 0; y < S.create.length; ++y) S.create[y](ha, i.parent)
                        }
                        t(m) ? b(m, [r], 0, 0) : t(r.tag) && _(r)
                    }
                }
                return w(i, p, f), i.elm
            }
        }({nodeOps: da, modules: Ka});
    Wi && document.addEventListener("selectionchange", function () {
        var e = document.activeElement;
        e && e.vmodel && or(e, "input")
    });
    var Wa = {
        inserted: function (e, t, n) {
            if ("select" === n.tag) {
                var r = function () {
                    er(e, t, n.context)
                };
                r(), (qi || Zi) && setTimeout(r, 0)
            } else "textarea" !== n.tag && "text" !== e.type && "password" !== e.type || (e._vModifiers = t.modifiers, t.modifiers.lazy || (e.addEventListener("change", ir), Gi || (e.addEventListener("compositionstart", rr), e.addEventListener("compositionend", ir)), Wi && (e.vmodel = !0)))
        }, componentUpdated: function (e, t, n) {
            if ("select" === n.tag) {
                er(e, t, n.context);
                (e.multiple ? t.value.some(function (t) {
                    return tr(t, e.options)
                }) : t.value !== t.oldValue && tr(t.value, e.options)) && or(e, "change")
            }
        }
    }, Za = {
        bind: function (e, t, n) {
            var r = t.value;
            n = ar(n);
            var i = n.data && n.data.transition,
                o = e.__vOriginalDisplay = "none" === e.style.display ? "" : e.style.display;
            r && i && !Wi ? (n.data.show = !0, Zn(n, function () {
                e.style.display = o
            })) : e.style.display = r ? o : "none"
        }, update: function (e, t, n) {
            var r = t.value;
            r !== t.oldValue && (n = ar(n), n.data && n.data.transition && !Wi ? (n.data.show = !0, r ? Zn(n, function () {
                e.style.display = e.__vOriginalDisplay
            }) : Gn(n, function () {
                e.style.display = "none"
            })) : e.style.display = r ? e.__vOriginalDisplay : "none")
        }, unbind: function (e, t, n, r, i) {
            i || (e.style.display = e.__vOriginalDisplay)
        }
    }, Ga = {model: Wa, show: Za}, Ya = {
        name: String,
        appear: Boolean,
        css: Boolean,
        mode: String,
        type: String,
        enterClass: String,
        leaveClass: String,
        enterToClass: String,
        leaveToClass: String,
        enterActiveClass: String,
        leaveActiveClass: String,
        appearClass: String,
        appearActiveClass: String,
        appearToClass: String,
        duration: [Number, String, Object]
    }, Qa = {
        name: "transition", props: Ya, abstract: !0, render: function (e) {
            var t = this, n = this.$slots.default;
            if (n && (n = n.filter(function (e) {
                    return e.tag
                }), n.length)) {
                var r = this.mode, o = n[0];
                if (lr(this.$vnode)) return o;
                var a = sr(o);
                if (!a) return o;
                if (this._leaving) return ur(e, o);
                var s = "__transition-" + this._uid + "-";
                a.key = null == a.key ? s + a.tag : i(a.key) ? 0 === String(a.key).indexOf(s) ? a.key : s + a.key : a.key;
                var c = (a.data || (a.data = {})).transition = cr(this), u = this._vnode, l = sr(u);
                if (a.data.directives && a.data.directives.some(function (e) {
                        return "show" === e.name
                    }) && (a.data.show = !0), l && l.data && !fr(a, l)) {
                    var f = l && (l.data.transition = m({}, c));
                    if ("out-in" === r) return this._leaving = !0, Q(f, "afterLeave", function () {
                        t._leaving = !1, t.$forceUpdate()
                    }), ur(e, o);
                    if ("in-out" === r) {
                        var p, d = function () {
                            p()
                        };
                        Q(c, "afterEnter", d), Q(c, "enterCancelled", d), Q(f, "delayLeave", function (e) {
                            p = e
                        })
                    }
                }
                return o
            }
        }
    }, Xa = m({tag: String, moveClass: String}, Ya);
    delete Xa.mode;
    var es = {
        props: Xa, render: function (e) {
            for (var t = this.tag || this.$vnode.data.tag || "span", n = Object.create(null), r = this.prevChildren = this.children, i = this.$slots.default || [], o = this.children = [], a = cr(this), s = 0; s < i.length; s++) {
                var c = i[s];
                c.tag && null != c.key && 0 !== String(c.key).indexOf("__vlist") && (o.push(c), n[c.key] = c, (c.data || (c.data = {})).transition = a)
            }
            if (r) {
                for (var u = [], l = [], f = 0; f < r.length; f++) {
                    var p = r[f];
                    p.data.transition = a, p.data.pos = p.elm.getBoundingClientRect(), n[p.key] ? u.push(p) : l.push(p)
                }
                this.kept = e(t, null, u), this.removed = l
            }
            return e(t, null, o)
        }, beforeUpdate: function () {
            this.__patch__(this._vnode, this.kept, !1, !0), this._vnode = this.kept
        }, updated: function () {
            var e = this.prevChildren, t = this.moveClass || (this.name || "v") + "-move";
            if (e.length && this.hasMove(e[0].elm, t)) {
                e.forEach(pr), e.forEach(dr), e.forEach(vr);
                var n = document.body;
                n.offsetHeight;
                e.forEach(function (e) {
                    if (e.data.moved) {
                        var n = e.elm, r = n.style;
                        Vn(n, t), r.transform = r.WebkitTransform = r.transitionDuration = "", n.addEventListener(Fa, n._moveCb = function e(r) {
                            r && !/transform$/.test(r.propertyName) || (n.removeEventListener(Fa, e), n._moveCb = null, zn(n, t))
                        })
                    }
                })
            }
        }, methods: {
            hasMove: function (e, t) {
                if (!Da) return !1;
                if (null != this._hasMove) return this._hasMove;
                var n = e.cloneNode();
                e._transitionClasses && e._transitionClasses.forEach(function (e) {
                    Bn(n, e)
                }), Fn(n, t), n.style.display = "none", this.$el.appendChild(n);
                var r = Kn(n);
                return this.$el.removeChild(n), this._hasMove = r.hasTransform
            }
        }
    }, ts = {Transition: Qa, TransitionGroup: es};
    vt.config.mustUseProp = ea, vt.config.isReservedTag = fa, vt.config.isReservedAttr = Qo, vt.config.getTagNamespace = Et, vt.config.isUnknownElement = jt, m(vt.options.directives, Ga), m(vt.options.components, ts), vt.prototype.__patch__ = Ji ? qa : y, vt.prototype.$mount = function (e, t) {
        return e = e && Ji ? Nt(e) : void 0, me(this, e, t)
    }, setTimeout(function () {
        Bi.devtools && io && io.emit("init", vt)
    }, 0);
    var ns, rs = !!Ji && function (e, t) {
            var n = document.createElement("div");
            return n.innerHTML = '<div a="' + e + '">', n.innerHTML.indexOf(t) > 0
        }("\n", "&#10;"),
        is = l("area,base,br,col,embed,frame,hr,img,input,isindex,keygen,link,meta,param,source,track,wbr"),
        os = l("colgroup,dd,dt,li,options,p,td,tfoot,th,thead,tr,source"),
        as = l("address,article,aside,base,blockquote,body,caption,col,colgroup,dd,details,dialog,div,dl,dt,fieldset,figcaption,figure,footer,form,h1,h2,h3,h4,h5,h6,head,header,hgroup,hr,html,legend,li,menuitem,meta,optgroup,option,param,rp,rt,source,style,summary,tbody,td,tfoot,th,thead,title,tr,track"),
        ss = [/"([^"]*)"+/.source, /'([^']*)'+/.source, /([^\s"'=<>`]+)/.source],
        cs = new RegExp("^\\s*" + /([^\s"'<>\/=]+)/.source + "(?:\\s*(" + /(?:=)/.source + ")\\s*(?:" + ss.join("|") + "))?"),
        us = "[a-zA-Z_][\\w\\-\\.]*", ls = new RegExp("^<((?:" + us + "\\:)?" + us + ")"), fs = /^\s*(\/?)>/,
        ps = new RegExp("^<\\/((?:" + us + "\\:)?" + us + ")[^>]*>"), ds = /^<!DOCTYPE [^>]+>/i, vs = /^<!--/,
        hs = /^<!\[/, ms = !1;
    "x".replace(/x(.)?/g, function (e, t) {
        ms = "" === t
    });
    var gs, ys, _s, bs, $s, Cs, xs, ws, ks, As, Os, Ss, Ts, Es, js, Ns, Ls, Is, Ds = l("script,style,textarea", !0),
        Ms = {}, Ps = {"&lt;": "<", "&gt;": ">", "&quot;": '"', "&amp;": "&", "&#10;": "\n"},
        Rs = /&(?:lt|gt|quot|amp);/g, Fs = /&(?:lt|gt|quot|amp|#10);/g, Bs = /\{\{((?:.|\n)+?)\}\}/g,
        Hs = d(function (e) {
            var t = e[0].replace(/[-.*+?^${}()|[\]\/\\]/g, "\\$&"), n = e[1].replace(/[-.*+?^${}()|[\]\/\\]/g, "\\$&");
            return new RegExp(t + "((?:.|\\n)+?)" + n, "g")
        }), Us = /^@|^v-on:/, Vs = /^v-|^@|^:/, zs = /(.*?)\s+(?:in|of)\s+(.*)/,
        Js = /\((\{[^}]*\}|[^,]*),([^,]*)(?:,([^,]*))?\)/, Ks = /:(.*)$/, qs = /^:|^v-bind:/, Ws = /\.[^.]+/g,
        Zs = d(hr), Gs = /^xmlns:NS\d+/, Ys = /^NS\d+:/, Qs = d(Br),
        Xs = /^\s*([\w$_]+|\([^)]*?\))\s*=>|^function\s*\(/,
        ec = /^\s*[A-Za-z_$][\w$]*(?:\.[A-Za-z_$][\w$]*|\['.*?']|\[".*?"]|\[\d+]|\[[A-Za-z_$][\w$]*])*\s*$/,
        tc = {esc: 27, tab: 9, enter: 13, space: 32, up: 38, left: 37, right: 39, down: 40, delete: [8, 46]},
        nc = function (e) {
            return "if(" + e + ")return null;"
        }, rc = {
            stop: "$event.stopPropagation();",
            prevent: "$event.preventDefault();",
            self: nc("$event.target !== $event.currentTarget"),
            ctrl: nc("!$event.ctrlKey"),
            shift: nc("!$event.shiftKey"),
            alt: nc("!$event.altKey"),
            meta: nc("!$event.metaKey"),
            left: nc("'button' in $event && $event.button !== 0"),
            middle: nc("'button' in $event && $event.button !== 1"),
            right: nc("'button' in $event && $event.button !== 2")
        }, ic = {bind: Gr, cloak: y}, oc = {staticKeys: ["staticClass"], transformNode: Ci, genData: xi},
        ac = {staticKeys: ["staticStyle"], transformNode: wi, genData: ki}, sc = [oc, ac],
        cc = {model: Cn, text: Ai, html: Oi}, uc = {
            expectHTML: !0,
            modules: sc,
            directives: cc,
            isPreTag: la,
            isUnaryTag: is,
            mustUseProp: ea,
            canBeLeftOpenTag: os,
            isReservedTag: fa,
            getTagNamespace: Et,
            staticKeys: function (e) {
                return e.reduce(function (e, t) {
                    return e.concat(t.staticKeys || [])
                }, []).join(",")
            }(sc)
        }, lc = function (e) {
            function t(t, n) {
                var r = Object.create(e), i = [], o = [];
                if (r.warn = function (e, t) {
                        (t ? o : i).push(e)
                    }, n) {
                    n.modules && (r.modules = (e.modules || []).concat(n.modules)), n.directives && (r.directives = m(Object.create(e.directives), n.directives));
                    for (var a in n) "modules" !== a && "directives" !== a && (r[a] = n[a])
                }
                var s = bi(t, r);
                return s.errors = i, s.tips = o, s
            }

            function n(e, n, i) {
                n = n || {};
                var o = n.delimiters ? String(n.delimiters) + e : e;
                if (r[o]) return r[o];
                var a = t(e, n), s = {}, c = [];
                s.render = $i(a.render, c);
                var u = a.staticRenderFns.length;
                s.staticRenderFns = new Array(u);
                for (var l = 0; l < u; l++) s.staticRenderFns[l] = $i(a.staticRenderFns[l], c);
                return r[o] = s
            }

            var r = Object.create(null);
            return {compile: t, compileToFunctions: n}
        }(uc), fc = lc.compileToFunctions, pc = d(function (e) {
            var t = Nt(e);
            return t && t.innerHTML
        }), dc = vt.prototype.$mount;
    return vt.prototype.$mount = function (e, t) {
        if ((e = e && Nt(e)) === document.body || e === document.documentElement) return this;
        var n = this.$options;
        if (!n.render) {
            var r = n.template;
            if (r) if ("string" == typeof r) "#" === r.charAt(0) && (r = pc(r)); else {
                if (!r.nodeType) return this;
                r = r.innerHTML
            } else e && (r = Si(e));
            if (r) {
                var i = fc(r, {shouldDecodeNewlines: rs, delimiters: n.delimiters}, this), o = i.render,
                    a = i.staticRenderFns;
                n.render = o, n.staticRenderFns = a
            }
        }
        return dc.call(this, e, t)
    }, vt.compile = fc, vt
});
/*!
 * vue-resource v1.2.1
 * https://github.com/pagekit/vue-resource
 * Released under the MIT License.
 */

!function (t, e) {
    "object" == typeof exports && "undefined" != typeof module ? module.exports = e() : "function" == typeof define && define.amd ? define(e) : t.VueResource = e()
}(this, function () {
    "use strict";

    function t(t) {
        this.state = J, this.value = void 0, this.deferred = [];
        var e = this;
        try {
            t(function (t) {
                e.resolve(t)
            }, function (t) {
                e.reject(t)
            })
        } catch (t) {
            e.reject(t)
        }
    }

    function e(t, e) {
        t instanceof Promise ? this.promise = t : this.promise = new Promise(t.bind(e)), this.context = e
    }

    function n(t) {
        "undefined" != typeof console && Q && console.warn("[VueResource warn]: " + t)
    }

    function o(t) {
        "undefined" != typeof console && console.error(t)
    }

    function r(t, e) {
        return G(t, e)
    }

    function i(t) {
        return t ? t.replace(/^\s*|\s*$/g, "") : ""
    }

    function u(t) {
        return t ? t.toLowerCase() : ""
    }

    function s(t) {
        return t ? t.toUpperCase() : ""
    }

    function a(t) {
        return "string" == typeof t
    }

    function c(t) {
        return "function" == typeof t
    }

    function f(t) {
        return null !== t && "object" == typeof t
    }

    function h(t) {
        return f(t) && Object.getPrototypeOf(t) == Object.prototype
    }

    function p(t) {
        return "undefined" != typeof Blob && t instanceof Blob
    }

    function d(t) {
        return "undefined" != typeof FormData && t instanceof FormData
    }

    function l(t, n, o) {
        var r = e.resolve(t);
        return arguments.length < 2 ? r : r.then(n, o)
    }

    function m(t, e, n) {
        return n = n || {}, c(n) && (n = n.call(e)), v(t.bind({$vm: e, $options: n}), t, {$options: n})
    }

    function y(t, e) {
        var n, o;
        if (tt(t)) for (n = 0; n < t.length; n++) e.call(t[n], t[n], n); else if (f(t)) for (o in t) _.call(t, o) && e.call(t[o], t[o], o);
        return t
    }

    function v(t) {
        var e = K.call(arguments, 1);
        return e.forEach(function (e) {
            w(t, e, !0)
        }), t
    }

    function b(t) {
        var e = K.call(arguments, 1);
        return e.forEach(function (e) {
            for (var n in e) void 0 === t[n] && (t[n] = e[n])
        }), t
    }

    function g(t) {
        var e = K.call(arguments, 1);
        return e.forEach(function (e) {
            w(t, e)
        }), t
    }

    function w(t, e, n) {
        for (var o in e) n && (h(e[o]) || tt(e[o])) ? (h(e[o]) && !h(t[o]) && (t[o] = {}), tt(e[o]) && !tt(t[o]) && (t[o] = []), w(t[o], e[o], n)) : void 0 !== e[o] && (t[o] = e[o])
    }

    function T(t, e, n) {
        var o = x(t), r = o.expand(e);
        return n && n.push.apply(n, o.vars), r
    }

    function x(t) {
        var e = ["+", "#", ".", "/", ";", "?", "&"], n = [];
        return {
            vars: n, expand: function (o) {
                return t.replace(/\{([^\{\}]+)\}|([^\{\}]+)/g, function (t, r, i) {
                    if (r) {
                        var u = null, s = [];
                        if (e.indexOf(r.charAt(0)) !== -1 && (u = r.charAt(0), r = r.substr(1)), r.split(/,/g).forEach(function (t) {
                                var e = /([^:\*]*)(?::(\d+)|(\*))?/.exec(t);
                                s.push.apply(s, j(o, u, e[1], e[2] || e[3])), n.push(e[1])
                            }), u && "+" !== u) {
                            var a = ",";
                            return "?" === u ? a = "&" : "#" !== u && (a = u), (0 !== s.length ? u : "") + s.join(a)
                        }
                        return s.join(",")
                    }
                    return C(i)
                })
            }
        }
    }

    function j(t, e, n, o) {
        var r = t[n], i = [];
        if (E(r) && "" !== r) if ("string" == typeof r || "number" == typeof r || "boolean" == typeof r) r = r.toString(), o && "*" !== o && (r = r.substring(0, parseInt(o, 10))), i.push(P(e, r, O(e) ? n : null)); else if ("*" === o) Array.isArray(r) ? r.filter(E).forEach(function (t) {
            i.push(P(e, t, O(e) ? n : null))
        }) : Object.keys(r).forEach(function (t) {
            E(r[t]) && i.push(P(e, r[t], t))
        }); else {
            var u = [];
            Array.isArray(r) ? r.filter(E).forEach(function (t) {
                u.push(P(e, t))
            }) : Object.keys(r).forEach(function (t) {
                E(r[t]) && (u.push(encodeURIComponent(t)), u.push(P(e, r[t].toString())))
            }), O(e) ? i.push(encodeURIComponent(n) + "=" + u.join(",")) : 0 !== u.length && i.push(u.join(","))
        } else ";" === e ? i.push(encodeURIComponent(n)) : "" !== r || "&" !== e && "?" !== e ? "" === r && i.push("") : i.push(encodeURIComponent(n) + "=");
        return i
    }

    function E(t) {
        return void 0 !== t && null !== t
    }

    function O(t) {
        return ";" === t || "&" === t || "?" === t
    }

    function P(t, e, n) {
        return e = "+" === t || "#" === t ? C(e) : encodeURIComponent(e), n ? encodeURIComponent(n) + "=" + e : e
    }

    function C(t) {
        return t.split(/(%[0-9A-Fa-f]{2})/g).map(function (t) {
            return /%[0-9A-Fa-f]/.test(t) || (t = encodeURI(t)), t
        }).join("")
    }

    function $(t, e) {
        var n, o = this || {}, r = t;
        return a(t) && (r = {
            url: t,
            params: e
        }), r = v({}, $.options, o.$options, r), $.transforms.forEach(function (t) {
            n = U(t, n, o.$vm)
        }), n(r)
    }

    function U(t, e, n) {
        return function (o) {
            return t.call(n, o, e)
        }
    }

    function R(t, e, n) {
        var o, r = tt(e), i = h(e);
        y(e, function (e, u) {
            o = f(e) || tt(e), n && (u = n + "[" + (i || o ? u : "") + "]"), !n && r ? t.add(e.name, e.value) : o ? R(t, e, u) : t.add(u, e)
        })
    }

    function A(t) {
        var e = t.match(/^\[|^\{(?!\{)/), n = {"[": /]$/, "{": /}$/};
        return e && n[e[0]].test(t)
    }

    function S(t, e) {
        var n = t.client || (Y ? mt : yt);
        e(n(t))
    }

    function k(t, e) {
        return Object.keys(t).reduce(function (t, n) {
            return u(e) === u(n) ? n : t
        }, null)
    }

    function I(t) {
        if (/[^a-z0-9\-#$%&'*+.\^_`|~]/i.test(t)) throw new TypeError("Invalid character in header field name");
        return i(t)
    }

    function H(t) {
        return new e(function (e) {
            var n = new FileReader;
            n.readAsText(t), n.onload = function () {
                e(n.result)
            }
        })
    }

    function B(t) {
        return 0 === t.type.indexOf("text") || t.type.indexOf("json") !== -1
    }

    function L(t) {
        var n = this || {}, r = vt(n.$vm);
        return b(t || {}, n.$options, L.options), L.interceptors.forEach(function (t) {
            r.use(t)
        }), r(new wt(t)).then(function (t) {
            return t.ok ? t : e.reject(t)
        }, function (t) {
            return t instanceof Error && o(t), e.reject(t)
        })
    }

    function q(t, e, n, o) {
        var r = this || {}, i = {};
        return n = et({}, q.actions, n), y(n, function (n, u) {
            n = v({url: t, params: et({}, e)}, o, n), i[u] = function () {
                return (r.$http || L)(M(n, arguments))
            }
        }), i
    }

    function M(t, e) {
        var n, o = et({}, t), r = {};
        switch (e.length) {
            case 2:
                r = e[0], n = e[1];
                break;
            case 1:
                /^(POST|PUT|PATCH)$/i.test(o.method) ? n = e[0] : r = e[0];
                break;
            case 0:
                break;
            default:
                throw"Expected up to 2 arguments [params, body], got " + e.length + " arguments"
        }
        return o.body = n, o.params = et({}, o.params, r), o
    }

    function N(t) {
        N.installed || (Z(t), t.url = $, t.http = L, t.resource = q, t.Promise = e, Object.defineProperties(t.prototype, {
            $url: {
                get: function () {
                    return m(t.url, this, this.$options.url)
                }
            }, $http: {
                get: function () {
                    return m(t.http, this, this.$options.http)
                }
            }, $resource: {
                get: function () {
                    return t.resource.bind(this)
                }
            }, $promise: {
                get: function () {
                    var e = this;
                    return function (n) {
                        return new t.Promise(n, e)
                    }
                }
            }
        }))
    }

    var D = 0, F = 1, J = 2;
    t.reject = function (e) {
        return new t(function (t, n) {
            n(e)
        })
    }, t.resolve = function (e) {
        return new t(function (t, n) {
            t(e)
        })
    }, t.all = function (e) {
        return new t(function (n, o) {
            function r(t) {
                return function (o) {
                    u[t] = o, i += 1, i === e.length && n(u)
                }
            }

            var i = 0, u = [];
            0 === e.length && n(u);
            for (var s = 0; s < e.length; s += 1) t.resolve(e[s]).then(r(s), o)
        })
    }, t.race = function (e) {
        return new t(function (n, o) {
            for (var r = 0; r < e.length; r += 1) t.resolve(e[r]).then(n, o)
        })
    };
    var W = t.prototype;
    W.resolve = function (t) {
        var e = this;
        if (e.state === J) {
            if (t === e) throw new TypeError("Promise settled with itself.");
            var n = !1;
            try {
                var o = t && t.then;
                if (null !== t && "object" == typeof t && "function" == typeof o) return void o.call(t, function (t) {
                    n || e.resolve(t), n = !0
                }, function (t) {
                    n || e.reject(t), n = !0
                })
            } catch (t) {
                return void(n || e.reject(t))
            }
            e.state = D, e.value = t, e.notify()
        }
    }, W.reject = function (t) {
        var e = this;
        if (e.state === J) {
            if (t === e) throw new TypeError("Promise settled with itself.");
            e.state = F, e.value = t, e.notify()
        }
    }, W.notify = function () {
        var t = this;
        r(function () {
            if (t.state !== J) for (; t.deferred.length;) {
                var e = t.deferred.shift(), n = e[0], o = e[1], r = e[2], i = e[3];
                try {
                    t.state === D ? r("function" == typeof n ? n.call(void 0, t.value) : t.value) : t.state === F && ("function" == typeof o ? r(o.call(void 0, t.value)) : i(t.value))
                } catch (t) {
                    i(t)
                }
            }
        })
    }, W.then = function (e, n) {
        var o = this;
        return new t(function (t, r) {
            o.deferred.push([e, n, t, r]), o.notify()
        })
    }, W.catch = function (t) {
        return this.then(void 0, t)
    }, "undefined" == typeof Promise && (window.Promise = t), e.all = function (t, n) {
        return new e(Promise.all(t), n)
    }, e.resolve = function (t, n) {
        return new e(Promise.resolve(t), n)
    }, e.reject = function (t, n) {
        return new e(Promise.reject(t), n)
    }, e.race = function (t, n) {
        return new e(Promise.race(t), n)
    };
    var X = e.prototype;
    X.bind = function (t) {
        return this.context = t, this
    }, X.then = function (t, n) {
        return t && t.bind && this.context && (t = t.bind(this.context)), n && n.bind && this.context && (n = n.bind(this.context)), new e(this.promise.then(t, n), this.context)
    }, X.catch = function (t) {
        return t && t.bind && this.context && (t = t.bind(this.context)), new e(this.promise.catch(t), this.context)
    }, X.finally = function (t) {
        return this.then(function (e) {
            return t.call(this), e
        }, function (e) {
            return t.call(this), Promise.reject(e)
        })
    };
    var G, V = {}, _ = V.hasOwnProperty, z = [], K = z.slice, Q = !1, Y = "undefined" != typeof window,
        Z = function (t) {
            var e = t.config, n = t.nextTick;
            G = n, Q = e.debug || !e.silent
        }, tt = Array.isArray, et = Object.assign || g, nt = function (t, e) {
            var n = e(t);
            return a(t.root) && !n.match(/^(https?:)?\//) && (n = t.root + "/" + n), n
        }, ot = function (t, e) {
            var n = Object.keys($.options.params), o = {}, r = e(t);
            return y(t.params, function (t, e) {
                n.indexOf(e) === -1 && (o[e] = t)
            }), o = $.params(o), o && (r += (r.indexOf("?") == -1 ? "?" : "&") + o), r
        }, rt = function (t) {
            var e = [], n = T(t.url, t.params, e);
            return e.forEach(function (e) {
                delete t.params[e]
            }), n
        };
    $.options = {url: "", root: null, params: {}}, $.transforms = [rt, ot, nt], $.params = function (t) {
        var e = [], n = encodeURIComponent;
        return e.add = function (t, e) {
            c(e) && (e = e()), null === e && (e = ""), this.push(n(t) + "=" + n(e))
        }, R(e, t), e.join("&").replace(/%20/g, "+")
    }, $.parse = function (t) {
        var e = document.createElement("a");
        return document.documentMode && (e.href = t, t = e.href), e.href = t, {
            href: e.href,
            protocol: e.protocol ? e.protocol.replace(/:$/, "") : "",
            port: e.port,
            host: e.host,
            hostname: e.hostname,
            pathname: "/" === e.pathname.charAt(0) ? e.pathname : "/" + e.pathname,
            search: e.search ? e.search.replace(/^\?/, "") : "",
            hash: e.hash ? e.hash.replace(/^#/, "") : ""
        }
    };
    var it = function (t) {
        return new e(function (e) {
            var n = new XDomainRequest, o = function (o) {
                var r = o.type, i = 0;
                "load" === r ? i = 200 : "error" === r && (i = 500), e(t.respondWith(n.responseText, {status: i}))
            };
            t.abort = function () {
                return n.abort()
            }, n.open(t.method, t.getUrl()), t.timeout && (n.timeout = t.timeout), n.onload = o, n.onabort = o, n.onerror = o, n.ontimeout = o, n.onprogress = function () {
            }, n.send(t.getBody())
        })
    }, ut = Y && "withCredentials" in new XMLHttpRequest, st = function (t, e) {
        if (Y) {
            var n = $.parse(location.href), o = $.parse(t.getUrl());
            o.protocol === n.protocol && o.host === n.host || (t.crossOrigin = !0, t.emulateHTTP = !1, ut || (t.client = it))
        }
        e()
    }, at = function (t, e) {
        d(t.body) ? t.headers.delete("Content-Type") : (f(t.body) || tt(t.body)) && (t.emulateJSON ? (t.body = $.params(t.body), t.headers.set("Content-Type", "application/x-www-form-urlencoded")) : t.body = JSON.stringify(t.body)), e(function (t) {
            return Object.defineProperty(t, "data", {
                get: function () {
                    return this.body
                }, set: function (t) {
                    this.body = t
                }
            }), t.bodyText ? l(t.text(), function (e) {
                var n = t.headers.get("Content-Type") || "";
                if (0 === n.indexOf("application/json") || A(e)) try {
                    t.body = JSON.parse(e)
                } catch (e) {
                    t.body = null
                } else t.body = e;
                return t
            }) : t
        })
    }, ct = function (t) {
        return new e(function (e) {
            var n, o, r = t.jsonp || "callback", i = t.jsonpCallback || "_jsonp" + Math.random().toString(36).substr(2),
                u = null;
            n = function (n) {
                var r = n.type, s = 0;
                "load" === r && null !== u ? s = 200 : "error" === r && (s = 500), s && window[i] && (delete window[i], document.body.removeChild(o)), e(t.respondWith(u, {status: s}))
            }, window[i] = function (t) {
                u = JSON.stringify(t)
            }, t.abort = function () {
                n({type: "abort"})
            }, t.params[r] = i, t.timeout && setTimeout(t.abort, t.timeout), o = document.createElement("script"), o.src = t.getUrl(), o.type = "text/javascript", o.async = !0, o.onload = n, o.onerror = n, document.body.appendChild(o)
        })
    }, ft = function (t, e) {
        "JSONP" == t.method && (t.client = ct), e()
    }, ht = function (t, e) {
        c(t.before) && t.before.call(this, t), e()
    }, pt = function (t, e) {
        t.emulateHTTP && /^(PUT|PATCH|DELETE)$/i.test(t.method) && (t.headers.set("X-HTTP-Method-Override", t.method), t.method = "POST"), e()
    }, dt = function (t, e) {
        var n = et({}, L.headers.common, t.crossOrigin ? {} : L.headers.custom, L.headers[u(t.method)]);
        y(n, function (e, n) {
            t.headers.has(n) || t.headers.set(n, e)
        }), e()
    }, lt = "undefined" != typeof Blob && "undefined" != typeof FileReader, mt = function (t) {
        return new e(function (e) {
            var n = new XMLHttpRequest, o = function (o) {
                var r = t.respondWith("response" in n ? n.response : n.responseText, {
                    status: 1223 === n.status ? 204 : n.status,
                    statusText: 1223 === n.status ? "No Content" : i(n.statusText)
                });
                y(i(n.getAllResponseHeaders()).split("\n"), function (t) {
                    r.headers.append(t.slice(0, t.indexOf(":")), t.slice(t.indexOf(":") + 1))
                }), e(r)
            };
            t.abort = function () {
                return n.abort()
            }, t.progress && ("GET" === t.method ? n.addEventListener("progress", t.progress) : /^(POST|PUT)$/i.test(t.method) && n.upload.addEventListener("progress", t.progress)), n.open(t.method, t.getUrl(), !0), t.timeout && (n.timeout = t.timeout), t.credentials === !0 && (n.withCredentials = !0), t.crossOrigin || t.headers.set("X-Requested-With", "XMLHttpRequest"), "responseType" in n && lt && (n.responseType = "blob"), t.headers.forEach(function (t, e) {
                n.setRequestHeader(e, t)
            }), n.onload = o, n.onabort = o, n.onerror = o, n.ontimeout = o, n.send(t.getBody())
        })
    }, yt = function (t) {
        var n = require("got");
        return new e(function (e) {
            var o, r = t.getUrl(), u = t.getBody(), s = t.method, a = {};
            t.headers.forEach(function (t, e) {
                a[e] = t
            }), n(r, {body: u, method: s, headers: a}).then(o = function (n) {
                var o = t.respondWith(n.body, {status: n.statusCode, statusText: i(n.statusMessage)});
                y(n.headers, function (t, e) {
                    o.headers.set(e, t)
                }), e(o)
            }, function (t) {
                return o(t.response)
            })
        })
    }, vt = function (t) {
        function o(o) {
            return new e(function (e) {
                function s() {
                    r = i.pop(), c(r) ? r.call(t, o, a) : (n("Invalid interceptor of type " + typeof r + ", must be a function"), a())
                }

                function a(n) {
                    if (c(n)) u.unshift(n); else if (f(n)) return u.forEach(function (e) {
                        n = l(n, function (n) {
                            return e.call(t, n) || n
                        })
                    }), void l(n, e);
                    s()
                }

                s()
            }, t)
        }

        var r, i = [S], u = [];
        return f(t) || (t = null), o.use = function (t) {
            i.push(t)
        }, o
    }, bt = function (t) {
        var e = this;
        this.map = {}, y(t, function (t, n) {
            return e.append(n, t)
        })
    };
    bt.prototype.has = function (t) {
        return null !== k(this.map, t)
    }, bt.prototype.get = function (t) {
        var e = this.map[k(this.map, t)];
        return e ? e.join() : null
    }, bt.prototype.getAll = function (t) {
        return this.map[k(this.map, t)] || []
    }, bt.prototype.set = function (t, e) {
        this.map[I(k(this.map, t) || t)] = [i(e)]
    }, bt.prototype.append = function (t, e) {
        var n = this.map[k(this.map, t)];
        n ? n.push(i(e)) : this.set(t, e)
    }, bt.prototype.delete = function (t) {
        delete this.map[k(this.map, t)]
    }, bt.prototype.deleteAll = function () {
        this.map = {}
    }, bt.prototype.forEach = function (t, e) {
        var n = this;
        y(this.map, function (o, r) {
            y(o, function (o) {
                return t.call(e, o, r, n)
            })
        })
    };
    var gt = function (t, e) {
        var n = e.url, o = e.headers, r = e.status, i = e.statusText;
        this.url = n, this.ok = r >= 200 && r < 300, this.status = r || 0, this.statusText = i || "", this.headers = new bt(o), this.body = t, a(t) ? this.bodyText = t : p(t) && (this.bodyBlob = t, B(t) && (this.bodyText = H(t)))
    };
    gt.prototype.blob = function () {
        return l(this.bodyBlob)
    }, gt.prototype.text = function () {
        return l(this.bodyText)
    }, gt.prototype.json = function () {
        return l(this.text(), function (t) {
            return JSON.parse(t)
        })
    };
    var wt = function (t) {
        this.body = null, this.params = {}, et(this, t, {method: s(t.method || "GET")}), this.headers instanceof bt || (this.headers = new bt(this.headers))
    };
    wt.prototype.getUrl = function () {
        return $(this)
    }, wt.prototype.getBody = function () {
        return this.body
    }, wt.prototype.respondWith = function (t, e) {
        return new gt(t, et(e || {}, {url: this.getUrl()}))
    };
    var Tt = {Accept: "application/json, text/plain, */*"}, xt = {"Content-Type": "application/json;charset=utf-8"};
    return L.options = {}, L.headers = {
        put: xt,
        post: xt,
        patch: xt,
        delete: xt,
        common: Tt,
        custom: {}
    }, L.interceptors = [ht, pt, at, ft, dt, st], ["get", "delete", "head", "jsonp"].forEach(function (t) {
        L[t] = function (e, n) {
            return this(et(n || {}, {url: e, method: t}))
        }
    }), ["post", "put", "patch"].forEach(function (t) {
        L[t] = function (e, n, o) {
            return this(et(o || {}, {url: e, method: t, body: n}))
        }
    }), q.actions = {
        get: {method: "GET"},
        save: {method: "POST"},
        query: {method: "GET"},
        update: {method: "PUT"},
        remove: {method: "DELETE"},
        delete: {method: "DELETE"}
    }, "undefined" != typeof window && window.Vue && window.Vue.use(N), N
});

(function ($) {
    var rotateLeft = function (lValue, iShiftBits) {
        return (lValue << iShiftBits) | (lValue >>> (32 - iShiftBits));
    }
    var addUnsigned = function (lX, lY) {
        var lX4, lY4, lX8, lY8, lResult;
        lX8 = (lX & 0x80000000);
        lY8 = (lY & 0x80000000);
        lX4 = (lX & 0x40000000);
        lY4 = (lY & 0x40000000);
        lResult = (lX & 0x3FFFFFFF) + (lY & 0x3FFFFFFF);
        if (lX4 & lY4) return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
        if (lX4 | lY4) {
            if (lResult & 0x40000000) return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
            else return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
        } else {
            return (lResult ^ lX8 ^ lY8);
        }
    }
    var F = function (x, y, z) {
        return (x & y) | ((~x) & z);
    }
    var G = function (x, y, z) {
        return (x & z) | (y & (~z));
    }
    var H = function (x, y, z) {
        return (x ^ y ^ z);
    }
    var I = function (x, y, z) {
        return (y ^ (x | (~z)));
    }
    var FF = function (a, b, c, d, x, s, ac) {
        a = addUnsigned(a, addUnsigned(addUnsigned(F(b, c, d), x), ac));
        return addUnsigned(rotateLeft(a, s), b);
    };
    var GG = function (a, b, c, d, x, s, ac) {
        a = addUnsigned(a, addUnsigned(addUnsigned(G(b, c, d), x), ac));
        return addUnsigned(rotateLeft(a, s), b);
    };
    var HH = function (a, b, c, d, x, s, ac) {
        a = addUnsigned(a, addUnsigned(addUnsigned(H(b, c, d), x), ac));
        return addUnsigned(rotateLeft(a, s), b);
    };
    var II = function (a, b, c, d, x, s, ac) {
        a = addUnsigned(a, addUnsigned(addUnsigned(I(b, c, d), x), ac));
        return addUnsigned(rotateLeft(a, s), b);
    };
    var convertToWordArray = function (string) {
        var lWordCount;
        var lMessageLength = string.length;
        var lNumberOfWordsTempOne = lMessageLength + 8;
        var lNumberOfWordsTempTwo = (lNumberOfWordsTempOne - (lNumberOfWordsTempOne % 64)) / 64;
        var lNumberOfWords = (lNumberOfWordsTempTwo + 1) * 16;
        var lWordArray = Array(lNumberOfWords - 1);
        var lBytePosition = 0;
        var lByteCount = 0;
        while (lByteCount < lMessageLength) {
            lWordCount = (lByteCount - (lByteCount % 4)) / 4;
            lBytePosition = (lByteCount % 4) * 8;
            lWordArray[lWordCount] = (lWordArray[lWordCount] | (string.charCodeAt(lByteCount) << lBytePosition));
            lByteCount++;
        }
        lWordCount = (lByteCount - (lByteCount % 4)) / 4;
        lBytePosition = (lByteCount % 4) * 8;
        lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80 << lBytePosition);
        lWordArray[lNumberOfWords - 2] = lMessageLength << 3;
        lWordArray[lNumberOfWords - 1] = lMessageLength >>> 29;
        return lWordArray;
    };
    var wordToHex = function (lValue) {
        var WordToHexValue = "", WordToHexValueTemp = "", lByte, lCount;
        for (lCount = 0; lCount <= 3; lCount++) {
            lByte = (lValue >>> (lCount * 8)) & 255;
            WordToHexValueTemp = "0" + lByte.toString(16);
            WordToHexValue = WordToHexValue + WordToHexValueTemp.substr(WordToHexValueTemp.length - 2, 2);
        }
        return WordToHexValue;
    };
    var uTF8Encode = function (string) {
        string = string.replace(/\x0d\x0a/g, "\x0a");
        var output = "";
        for (var n = 0; n < string.length; n++) {
            var c = string.charCodeAt(n);
            if (c < 128) {
                output += String.fromCharCode(c);
            } else if ((c > 127) && (c < 2048)) {
                output += String.fromCharCode((c >> 6) | 192);
                output += String.fromCharCode((c & 63) | 128);
            } else {
                output += String.fromCharCode((c >> 12) | 224);
                output += String.fromCharCode(((c >> 6) & 63) | 128);
                output += String.fromCharCode((c & 63) | 128);
            }
        }
        return output;
    };
    $.extend({
        md5: function (string) {
            var x = Array();
            var k, AA, BB, CC, DD, a, b, c, d;
            var S11 = 7, S12 = 12, S13 = 17, S14 = 22;
            var S21 = 5, S22 = 9, S23 = 14, S24 = 20;
            var S31 = 4, S32 = 11, S33 = 16, S34 = 23;
            var S41 = 6, S42 = 10, S43 = 15, S44 = 21;
            string = uTF8Encode(string);
            x = convertToWordArray(string);
            a = 0x67452301;
            b = 0xEFCDAB89;
            c = 0x98BADCFE;
            d = 0x10325476;
            for (k = 0; k < x.length; k += 16) {
                AA = a;
                BB = b;
                CC = c;
                DD = d;
                a = FF(a, b, c, d, x[k + 0], S11, 0xD76AA478);
                d = FF(d, a, b, c, x[k + 1], S12, 0xE8C7B756);
                c = FF(c, d, a, b, x[k + 2], S13, 0x242070DB);
                b = FF(b, c, d, a, x[k + 3], S14, 0xC1BDCEEE);
                a = FF(a, b, c, d, x[k + 4], S11, 0xF57C0FAF);
                d = FF(d, a, b, c, x[k + 5], S12, 0x4787C62A);
                c = FF(c, d, a, b, x[k + 6], S13, 0xA8304613);
                b = FF(b, c, d, a, x[k + 7], S14, 0xFD469501);
                a = FF(a, b, c, d, x[k + 8], S11, 0x698098D8);
                d = FF(d, a, b, c, x[k + 9], S12, 0x8B44F7AF);
                c = FF(c, d, a, b, x[k + 10], S13, 0xFFFF5BB1);
                b = FF(b, c, d, a, x[k + 11], S14, 0x895CD7BE);
                a = FF(a, b, c, d, x[k + 12], S11, 0x6B901122);
                d = FF(d, a, b, c, x[k + 13], S12, 0xFD987193);
                c = FF(c, d, a, b, x[k + 14], S13, 0xA679438E);
                b = FF(b, c, d, a, x[k + 15], S14, 0x49B40821);
                a = GG(a, b, c, d, x[k + 1], S21, 0xF61E2562);
                d = GG(d, a, b, c, x[k + 6], S22, 0xC040B340);
                c = GG(c, d, a, b, x[k + 11], S23, 0x265E5A51);
                b = GG(b, c, d, a, x[k + 0], S24, 0xE9B6C7AA);
                a = GG(a, b, c, d, x[k + 5], S21, 0xD62F105D);
                d = GG(d, a, b, c, x[k + 10], S22, 0x2441453);
                c = GG(c, d, a, b, x[k + 15], S23, 0xD8A1E681);
                b = GG(b, c, d, a, x[k + 4], S24, 0xE7D3FBC8);
                a = GG(a, b, c, d, x[k + 9], S21, 0x21E1CDE6);
                d = GG(d, a, b, c, x[k + 14], S22, 0xC33707D6);
                c = GG(c, d, a, b, x[k + 3], S23, 0xF4D50D87);
                b = GG(b, c, d, a, x[k + 8], S24, 0x455A14ED);
                a = GG(a, b, c, d, x[k + 13], S21, 0xA9E3E905);
                d = GG(d, a, b, c, x[k + 2], S22, 0xFCEFA3F8);
                c = GG(c, d, a, b, x[k + 7], S23, 0x676F02D9);
                b = GG(b, c, d, a, x[k + 12], S24, 0x8D2A4C8A);
                a = HH(a, b, c, d, x[k + 5], S31, 0xFFFA3942);
                d = HH(d, a, b, c, x[k + 8], S32, 0x8771F681);
                c = HH(c, d, a, b, x[k + 11], S33, 0x6D9D6122);
                b = HH(b, c, d, a, x[k + 14], S34, 0xFDE5380C);
                a = HH(a, b, c, d, x[k + 1], S31, 0xA4BEEA44);
                d = HH(d, a, b, c, x[k + 4], S32, 0x4BDECFA9);
                c = HH(c, d, a, b, x[k + 7], S33, 0xF6BB4B60);
                b = HH(b, c, d, a, x[k + 10], S34, 0xBEBFBC70);
                a = HH(a, b, c, d, x[k + 13], S31, 0x289B7EC6);
                d = HH(d, a, b, c, x[k + 0], S32, 0xEAA127FA);
                c = HH(c, d, a, b, x[k + 3], S33, 0xD4EF3085);
                b = HH(b, c, d, a, x[k + 6], S34, 0x4881D05);
                a = HH(a, b, c, d, x[k + 9], S31, 0xD9D4D039);
                d = HH(d, a, b, c, x[k + 12], S32, 0xE6DB99E5);
                c = HH(c, d, a, b, x[k + 15], S33, 0x1FA27CF8);
                b = HH(b, c, d, a, x[k + 2], S34, 0xC4AC5665);
                a = II(a, b, c, d, x[k + 0], S41, 0xF4292244);
                d = II(d, a, b, c, x[k + 7], S42, 0x432AFF97);
                c = II(c, d, a, b, x[k + 14], S43, 0xAB9423A7);
                b = II(b, c, d, a, x[k + 5], S44, 0xFC93A039);
                a = II(a, b, c, d, x[k + 12], S41, 0x655B59C3);
                d = II(d, a, b, c, x[k + 3], S42, 0x8F0CCC92);
                c = II(c, d, a, b, x[k + 10], S43, 0xFFEFF47D);
                b = II(b, c, d, a, x[k + 1], S44, 0x85845DD1);
                a = II(a, b, c, d, x[k + 8], S41, 0x6FA87E4F);
                d = II(d, a, b, c, x[k + 15], S42, 0xFE2CE6E0);
                c = II(c, d, a, b, x[k + 6], S43, 0xA3014314);
                b = II(b, c, d, a, x[k + 13], S44, 0x4E0811A1);
                a = II(a, b, c, d, x[k + 4], S41, 0xF7537E82);
                d = II(d, a, b, c, x[k + 11], S42, 0xBD3AF235);
                c = II(c, d, a, b, x[k + 2], S43, 0x2AD7D2BB);
                b = II(b, c, d, a, x[k + 9], S44, 0xEB86D391);
                a = addUnsigned(a, AA);
                b = addUnsigned(b, BB);
                c = addUnsigned(c, CC);
                d = addUnsigned(d, DD);
            }
            var tempValue = wordToHex(a) + wordToHex(b) + wordToHex(c) + wordToHex(d);
            return tempValue.toLowerCase();
        }
    });
})(jQuery);

var httpModule = {
    getAuthcode: function (phone) {
        var data = {"phone": phone};

        Vue.http.get(globalData.apiUrl + 'club/vcode?phone=' + phone).then(function (response) {
            logMessage(response.body);
            var bodyData = response.body;

            if (bodyData.code == 0) {
                appData.authcodeTime = 60;
                authcodeTimer();
                appData.authcodeType = 2;

            } else {
                viewMethods.clickShowAlert(7, bodyData.msg);
            }

        }, function (response) {
            viewMethods.clickShowAlert(7, '获取验证码失败');
        });
    },
    bindPhone: function (phone, authcode) {
        var data = {
            "dealer_num": globalData.dealerNum,
            "phone": phone,
            "code": authcode,
            "aid": userData.aid,
            "s": userData.s
        };

        Vue.http.post(globalData.baseUrl + 'account/checkSmsCode', data).then(function (response) {

            var bodyData = response.body;

            if (bodyData.result == 0) {
                appData.isShowBindPhone = false;
                appData.isPhone = true;
                appData.isAuthPhone = 0;
                appData.phone = appData.sPhone;

                if (bodyData.data.card_count != null && bodyData.data.card_count != undefined && bodyData.data.card_count != '') {
                    appData.roomCard = parseInt(appData.roomCard) + parseInt(bodyData.data.card_count);
                }

                if (bodyData.data.account_id != userData.accountId) {
                    viewMethods.clickShowAlert(23, bodyData.msg);
                } else {
                    viewMethods.clickShowAlert(23, bodyData.msg);
                }

                appData.sPhone = '';
                appData.sAuthcode = '';

            } else {
                viewMethods.clickShowAlert(7, bodyData.msg);
            }

        }, function (response) {
            appData.authcodeTime = 0;
            viewMethods.clickShowAlert(7, "绑定失败");
        });
    },
    modifyPwd: function (phone, authcode, pwd) {
        var data = {
            "phone": phone,
            "vcode": authcode,
            "secret": pwd,
        };

        Vue.http.post(globalData.apiUrl + 'club/secret', data).then(function (response) {

            var bodyData = response.body;

            if (bodyData.code == 0) {
                appData.isShowBindPhone = false;
                appData.isPhone = true;
                appData.isAuthPhone = 0;
                appData.phone = appData.sPhone;

                viewMethods.clickShowAlert(23, bodyData.msg);

                appData.sPhone = '';
                appData.sAuthcode = '';

            } else {
                viewMethods.clickShowAlert(7, bodyData.msg);
            }

        }, function (response) {
            appData.authcodeTime = 0;
            viewMethods.clickShowAlert(7, "操作失败");
        });
    },
};

var viewMethods = {
    clickShowAlert: function (type, text) {
        appData.alertType = type;
        appData.alertText = text;
        appData.isShowAlert = true;
        setTimeout(function () {
            var alertHeight = $(".alertText").height();
            var textHeight = alertHeight;
            if (alertHeight < height * 0.15) {
                alertHeight = height * 0.15;
            }

            if (alertHeight > height * 0.8) {
                alertHeight = height * 0.8;
            }

            var mainHeight = alertHeight + height * (0.022 + 0.034) * 2 + height * 0.022 + height * 0.056;
            if (type == 8) {
                mainHeight = mainHeight - height * 0.022 - height * 0.056
            }

            var blackHeight = alertHeight + height * 0.034 * 2;
            var alertTop = height * 0.022 + (blackHeight - textHeight) / 2;

            $(".alert .mainPart").css('height', mainHeight + 'px');
            $(".alert .mainPart").css('margin-top', '-' + mainHeight / 2 + 'px');
            $(".alert .mainPart .backImg .blackImg").css('height', blackHeight + 'px');
            $(".alert .mainPart .alertText").css('top', alertTop + 'px');
        }, 0);
    },
    clickCloseAlert: function () {
        appData.isShowAlert = false;
        if (appData.alertType == 1) {
            if (!appData.is_connect) {
                reconnectSocket();
                appData.is_connect = true;
            }
        }
    },
    showMessage: function () {
        $(".message .textPart").animate({
            height: "400px"
        });
        appData.isShowMessage = true;
    },
    hideMessage: function () {
        $(".message .textPart").animate({
            height: 0
        }, function () {
            appData.isShowMessage = false;
        });
    },
};

var width = window.innerWidth;
var height = window.innerHeight;
var isTimeLimitShow = false;
var viewOffset = 4;
var itemOffset = 4;
var itemHeight = 66 / 320 * width;
var leftOffset = 8 / 320 * width;
var userViewHeight = 0.25 * width;
var avatarWidth = 0.21875 * width;
var avatarY = (userViewHeight - avatarWidth) / 2;
var itemY = (80 + 44 * 2 + 40) / 320 * width + viewOffset * 3 + itemOffset;
var dtStartDate = '';
var dtEndDate = '';
var dtStartTimestamp = '0';
var dtEndTimestamp = '0';
var todayTimestamp = '0';
var groupOffset = 20;


var appData = {
    'userPhone': userData.phone,
    'createStep': 1,
    'width': window.innerWidth,
    'height': window.innerHeight,
    'roomCard': Math.ceil(globalData.card),
    'user': userData,
    'activity': [],
    'isShowInvite': false,
    'isShowAlert': false,
    'isShowMessage': false,
    'alertType': 0,
    'alertText': '',
    'isDealing': false,
    'gameItems': [],
    itemY: itemY,
    itemHeight: 66 / 320 * width,
    itemOffset: itemOffset,
    startDate: '',
    endDate: '',
    isPhone: false,
    isShowBindPhone: false,
    'isAuthPhone': userData.isAuthPhone,
    'authCardCount': userData.authCardCount,
    'phone': userData.phone,
    'sPhone': '',
    'sAuthcode': '',
    'sPwd': '',
    'authcodeType': 1,
    'authcodeText': '发送验证码',
    'authcodeTime': 60,
    'phoneType': 1,
    'phoneText': '绑定手机',
    'isShowGroupMenu': globalData.isShowGroupMenu,
    'gameScoreList': [],
    bScroll: null,
    page: 1,
    sumPage: 1,
    canLoadMore: true,
    selectedGame: null,
    isHttpRequest: false,
    cardText: globalData.cardText,
    isGetDogRP: 0,
    isShowDogRP: 0,
    canGetDogRP: 1,
    dogRPText: '',
};

appData.sPhone = userData.phone;
var result = checkPhone(appData.sPhone);
if (result) {
    $('#authcode').css('background-color', 'rgb(109,125,212)');
} else {
    $('#authcode').css('background-color', 'lightgray');
}

if (userData.phone != undefined && userData.phone.length >= 1) {
    logMessage(userData.phone);
    appData.isPhone = true;
    appData.phone = userData.phone;
    appData.phoneText = '修改手机号';
}

if (appData.isAuthPhone == 1) {
    appData.isShowBindPhone = true;
}

Date.prototype.format = function (fmt) {
    var o = {
        "M+": this.getMonth() + 1,                 //月份
        "d+": this.getDate(),                    //日
        "h+": this.getHours(),                   //小时
        "m+": this.getMinutes(),                 //分
        "s+": this.getSeconds(),                 //秒
        "q+": Math.floor((this.getMonth() + 3) / 3), //季度
        "S": this.getMilliseconds()             //毫秒
    };
    if (/(y+)/.test(fmt)) {
        fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    }
    for (var k in o) {
        if (new RegExp("(" + k + ")").test(fmt)) {
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
        }
    }
    return fmt;
};

convertTimestamp = function (date) {
    var timestamp = Date.parse(date);
    timestamp = timestamp / 1000;
    return timestamp;
}

function funDate(aa) {
    var date1 = new Date(),
        time1 = date1.getFullYear() + "-" + (date1.getMonth() + 1) + "-" + date1.getDate();
    var date2 = new Date(date1);
    date2.setDate(date1.getDate() + aa);

    var year = date2.getFullYear();
    var month = date2.getMonth() + 1;
    var day = date2.getDate();
    var time2 = year + '-';

    var monthS = month + '-';

    if (monthS.length < 3) {
        time2 = time2 + '0' + month + '-';
    } else {
        time2 = time2 + month + '-';
    }

    var dayS = day + '-';
    if (dayS.length < 3) {
        time2 = time2 + '0' + day;
    } else {
        time2 = time2 + day;
    }

    return time2;
}

//Vue方法
var methods = {
    showInvite: viewMethods.clickShowInvite,
    showAlert: viewMethods.clickShowAlert,
    showMessage: viewMethods.showMessage,
    closeInvite: viewMethods.clickCloseInvite,
    closeAlert: viewMethods.clickCloseAlert,
    getCards: viewMethods.clickGetCards,
    hideMessage: viewMethods.hideMessage,
    showRedpackageRecord: viewMethods.clickRedpackageRecord,
    showSendRedpackage: viewMethods.clickSendRedPackage,
    startDateChange: viewMethods.changeStartDate,
    endDateChange: viewMethods.changeEndDate,
    clickPhone: function () {
        appData.phoneText = '绑定手机';
        appData.phoneType = 1;
        appData.authcodeTime = 0;
        appData.authcodeText = '发送验证码';
        appData.authcodeType = 1;
        appData.isShowBindPhone = true;
    },
    hideBindPhone: function () {
        if (appData.phoneType == 1) {
            return;
        }

        appData.isShowBindPhone = false;
    },
    clickEditPhone: function () {
        appData.phoneText = '修改手机号';
        appData.phoneType = 2;
        appData.authcodeTime = 0;
        appData.authcodeText = '发送验证码';
        appData.authcodeType = 1;
        appData.isShowBindPhone = true;
    },
    bindPhone: function () {
        var validPhone = checkPhone(appData.sPhone);
        var validAuthcode = checkAuthcode(appData.sAuthcode);

        if (validPhone == false) {
            viewMethods.clickShowAlert(7, '手机号码有误，请重填');
            return;
        }

        if (validAuthcode == false) {
            viewMethods.clickShowAlert(7, '验证码有误，请重填');
            return;
        }

        httpModule.bindPhone(appData.sPhone, appData.sAuthcode);
    },
    modifyPwd: function () {
        var validPhone = checkPhone(appData.sPhone);
        var validAuthcode = checkAuthcode(appData.sAuthcode);
        var validPwd = checkPwd(appData.sPwd);

        if (validPhone == false) {
            viewMethods.clickShowAlert(7, '手机号码有误，请重填');
            return;
        }

        if (validAuthcode == false) {
            viewMethods.clickShowAlert(7, '验证码有误，请重填');
            return;
        }

        if (validPwd == false) {
            viewMethods.clickShowAlert(7, '密钥格式有误，请重填');
            return;
        }

        httpModule.modifyPwd(appData.sPhone, appData.sAuthcode, appData.sPwd);
    },
    getAuthcode: function () {
        if (appData.authcodeType != 1) {
            return;
        }

        var color = $('#authcode').css('background-color');
        if (color != 'rgb(109, 125, 212)') {
            return;
        }

        var validPhone = checkPhone(appData.sPhone);

        if (validPhone == false) {
            viewMethods.clickShowAlert(7, '手机号码有误，请重填');
            return;
        }

        httpModule.getAuthcode(appData.sPhone);
    },
    phoneChangeValue: function () {
        var result = checkPhone(appData.sPhone);
        if (result) {
            $('#authcode').css('background-color', 'rgb(109,125,212)');
        } else {
            $('#authcode').css('background-color', 'lightgray');
        }
    },
    finishBindPhone: function () {
        window.location.href = globalData.mhUrl;
    },
    nextStep: function () {
        appData.createStep = 2;
    },
    createTeam: function () {
        appData.createStep = 1;
    },
};

//Vue生命周期
var vueLife = {
    vmCreated: function () {
        logMessage('vmCreated')
        $("#loading").hide();
        $(".main").show();

    },
    vmUpdated: function () {
        logMessage('vmUpdated');
    },
    vmMounted: function () {
        logMessage('vmMounted');
    },
    vmDestroyed: function () {
        logMessage('vmDestroyed');
    }
};

//手机绑定******
function checkPhone(phone) {
    if (!(/^1(3|4|5|6|7|8|9)\d{9}$/.test(phone))) {
        return false;
    } else {
        return true;
    }
}

function checkAuthcode(code) {
    if (code == '' || code == undefined) {
        return false;
    }

    var reg = new RegExp("^[0-9]{4}$");
    if (!reg.test(code)) {
        return false;
    } else {
        return true;
    }
}

function checkPwd(pwd) {
    if (pwd == '' || pwd == undefined) {
        return false;
    }

    if (pwd.length < 6 || pwd.length > 20) {
        return false;
    }

    return true;
}

var authcodeTimer = function authcodeTimer() {
    if (appData.authcodeTime <= 0) {
        appData.authcodeText = '发送验证码';
        appData.authcodeTime = 60;
        appData.authcodeType = 1;
        return;
    }

    appData.authcodeTime = appData.authcodeTime - 1;
    appData.authcodeText = appData.authcodeTime + 's';

    setTimeout(function () {
        authcodeTimer();
    }, 1000);
};
//******手机绑定

$('#sPwd').on('touchstart', function () {
    $(this).focus();
    alert('hello');
})

$('.sPwd').on('touchstart', function () {
    $(this).focus();
    alert('hello');
})

console.log($('#sPwd'));

//Vue实例
var vm = new Vue({
    el: '#app-main',
    data: appData,
    methods: methods,
    created: vueLife.vmCreated,
    updated: vueLife.vmUpdated,
    mounted: vueLife.vmMounted,
    destroyed: vueLife.vmDestroyed,
});

function logMessage(message) {
    console.log(message);
};

