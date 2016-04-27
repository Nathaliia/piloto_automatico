void 0 == window.jQuery && alert("dzscalendar.js -> jQuery is not defined or improperly declared ( must be included at the start of the head tag ), you need jQuery for this plugin");
var settings_dzscalendar = {
    animation_time: 500,
    animation_easing: "swing"
};

function is_ios() {
    return -1 != navigator.platform.indexOf("iPhone") || -1 != navigator.platform.indexOf("iPod") || -1 != navigator.platform.indexOf("iPad")
}

function is_android() {
    return -1 != navigator.platform.indexOf("Android")
}

function is_ie() {
    return -1 != navigator.appVersion.indexOf("MSIE") ? !0 : !1
}

function is_firefox() {
    return -1 != navigator.userAgent.indexOf("Firefox") ? !0 : !1
}

function is_opera() {
    return -1 != navigator.userAgent.indexOf("Opera") ? !0 : !1
}

function is_chrome() {
    return -1 < navigator.userAgent.toLowerCase().indexOf("chrome")
}

function is_safari() {
    return -1 < navigator.userAgent.toLowerCase().indexOf("safari")
}

function version_ie() {
    return parseFloat(navigator.appVersion.split("MSIE")[1])
}

function version_firefox() {
    if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent)) return new Number(RegExp.$1)
}

function version_opera() {
    if (/Opera[\/\s](\d+\.\d+)/.test(navigator.userAgent)) return new Number(RegExp.$1)
}

function is_ie8() {
    return !0 == is_ie() && 9 > version_ie() ? !0 : !1
}
var $dzscal = jQuery.noConflict();
(function (D) {
    D.fn.dzscalendar = function (f) {
        f = D.extend({
            settings_slideshowTime: "5",
            settings_autoHeight: "on",
            settings_skin: "skin-default",
            start_month: "",
            start_year: "",
            start_weekday: "Sunday",
            design_transition: "slide",
            design_transitionDesc: "tooltipDef",
            header_weekdayStyle: "default"
        }, f);
        this.each(function () {
            function K() {
                "tooltipDef" == f.design_transitionDesc && c.find(".dzstooltip").each(function () {
                    var b = jQuery(this);
                    b.removeClass("currTooltip");
                    b.animate({
                        opacity: 0,
                        left: v + 50
                    }, {
                        queue: !1,
                        complete: P,
                        duration: settings_dzscalendar.animation_time,
                        easing: settings_dzscalendar.animation_easing
                    })
                });
                c.css("height", E)
            }

            function L() {
                var d = jQuery(this);
                if (0 != c.has(d).length)
                    if (d.hasClass("desc-close-button")) "slide" == f.design_transitionDesc && (j.animate({
                        top: -F
                    }, {
                        queue: !1,
                        duration: settings_dzscalendar.animation_time / 1.5,
                        easing: settings_dzscalendar.animation_easing
                    }), G.animate({
                        top: 0
                    }, {
                        queue: !1,
                        duration: settings_dzscalendar.animation_time / 1.5,
                        easing: settings_dzscalendar.animation_easing
                    }), m.animate({
                        top: 0
                    }, {
                        queue: !1,
                        duration: settings_dzscalendar.animation_time / 1.5,
                        easing: settings_dzscalendar.animation_easing
                    }));
                    else if (K(), d.hasClass("openTooltip")) d.removeClass("openTooltip");
                else {
                    var n = d.attr("data-date"),
                        a = d.attr("data-year"),
                        e = d.attr("data-month"),
                        l = d.attr("data-day"),
                        h = d.parent().parent().parent().parent().parent();
                    c.find(".openTooltip").each(function () {
                        jQuery(this).removeClass("openTooltip")
                    });
                    d.addClass("openTooltip");
                    var k = "";
                    for (b = 0; b < g.length; b++) g[b].date == n && (k += g[b].content), g[b].year == a && void 0 != a && void 0 != e && g[b].month == e && (void 0 != l && g[b].day ==
                        l ? k += g[b].content : void 0 != g[b].startday && (g[b].startday <= l && g[b].endday >= l) && (k += g[b].content)), "everymonth" == g[b].repeat && (console.log(g[b], g[b].day, l), g[b].day == l && (k += g[b].content));
                    v = d.offset().left - h.offset().left + d.outerWidth();
                    M = d.offset().top - h.offset().top;
                    "tooltipDef" == f.design_transitionDesc && (c.append('<div class="dzstooltip arrow-left currTooltip" style="left:' + (v - 10) + "px; top:" + M + 'px;"></div>'), d = c.children(".dzstooltip").last(), d.html(k), d.animate({
                        opacity: 1,
                        left: v + 5
                    }, {
                        queue: !1,
                        duration: settings_dzscalendar.animation_time / 1.5,
                        easing: settings_dzscalendar.animation_easing
                    }), d.height() + parseInt(d.css("top"), 10) > c.height() && (E = c.height(), c.css("height", d.height() + parseInt(d.css("top"), 10))));
                    "slide" == f.design_transitionDesc && (c.css({
                        overflow: "hidden"
                    }), c.append('<div class="currDesc slideDescription" style=""></div>'), j = c.find(".currDesc").eq(0), j.html(k), j.append('<div class="desc-close-button">x</div>'), j.css({
                        top: -F,
                        width: w
                    }), j.children("div").css({
                        width: "auto"
                    }), j.animate({
                        top: 0
                    }, {
                        queue: !1,
                        duration: settings_dzscalendar.animation_time / 1.5,
                        easing: settings_dzscalendar.animation_easing
                    }), G.animate({
                        top: z + 20
                    }, {
                        queue: !1,
                        duration: settings_dzscalendar.animation_time / 1.5,
                        easing: settings_dzscalendar.animation_easing
                    }), m.animate({
                        top: z + 20
                    }, {
                        queue: !1,
                        duration: settings_dzscalendar.animation_time / 1.5,
                        easing: settings_dzscalendar.animation_easing
                    }), j.children(".desc-close-button").bind("click", L))
                }
            }

            function P() {
                "tooltipDef" == f.design_transitionDesc && c.find(".dzstooltip").each(function () {
                    var b = jQuery(this);
                    !1 == b.hasClass("currTooltip") && b.remove()
                })
            }

            function H(d, n) {
                if ("ceva" == "ceva") {
                    var a = !1,
                        e = document.URL,
                        l = e.indexOf("://") + 3,
                        h = e.indexOf("/", l),
                        e = e.substring(l, h); - 1 < e.indexOf("a") && (-1 < e.indexOf("c") && -1 < e.indexOf("o") && -1 < e.indexOf("l")) && (a = !0); - 1 < e.indexOf("o") && (-1 < e.indexOf("z") && -1 < e.indexOf("e") && -1 < e.indexOf("h") && -1 < e.indexOf("t")) && (a = !0); - 1 < e.indexOf("e") && (-1 < e.indexOf("v") && -1 < e.indexOf("n") && -1 < e.indexOf("a") && -1 < e.indexOf("t")) && (a = !0);
                    if (!1 == a) return
                }
                h = new Date;
                if (!0 != A) {
                    A = !0;
                    h.setYear(d);
                    h.setMonth(n);
                    h.setDate(0);
                    e = n +
                        1;
                    l = d;
                    12 == e && (e = 0, l++);
                    var a = "<tr>",
                        k = h.getDay();
                    "Monday" == f.start_weekday && k--;
                    for (b = h = 0; b <= k; b++) {
                        var a = a + '<td class="other-months-date',
                            j = new Date(d, n, b + 2);
                        j < u && (a += " past-date");
                        a += " ldate";
                        a += '"';
                        a += ">";
                        a += (new Date(d, n, 0)).getDate() - k + b;
                        a += "</td>";
                        6 == h && (a += "</tr>", a += "<tr>", h = -1);
                        h++
                    }
                    for (b = 0; b < (new Date(l, e, 0)).getDate(); b++) {
                        a += '<td class="curr-months-date';
                        j = new Date(d, n, b + 2);
                        j < u && (a += " past-date");
						a += " ldate";
                        var k = n + 1 + "-" + (b + 1) + "-" + d,
                            j = d,
                            v = n + 1,
                            y = b + 1;
                        for (p = 0; p < g.length; p++) g[p].date == k && (a += " hasEvent"), g[p].year ==
                            j && g[p].month == v && (g[p].day == y ? a += " hasEvent" : void 0 != g[p].startday && (g[p].startday <= y && g[p].endday >= y) && (a += " hasEvent")), "everymonth" == g[p].repeat && g[p].day == y && (a += " hasEvent");
                        a += '"';
                        a += ' data-date="' + k + '"';
                        a += ' data-day="' + y + '"';
                        a += ' data-month="' + v + '"';
                        a += ' data-year="' + j + '"';
                        a += ">";
                        a += b + 1;
                        a += "</td>";
                        6 == h && (a += "</tr>", a += "<tr>", h = -1);
                        h++
                    }
                    if (0 < h)
                        for (b = 0; 7 > h; b++) a += '<td class="other-months-date', j = new Date(d, n, b + 2), j < u && (a += " past-date"), a += " ldate", a += '"', a += ">", a += b + 1, a += "</td>", h++;
                    a += "</tr>";
                    0 < m.children().length ?
                        (m.children().eq(0).removeClass("argTable"), m.children().eq(0).addClass("currTable"), d > r ? B = !0 : d < r ? B = !1 : d == r && (B = n < t ? !1 : !0)) : A = !1;
                    r = d;
                    t = n;
                    N = 0;
                    I.children(".curr-month").html(O[t]);
                    I.children(".curr-year").html(r);
                    e = "";
                    l = "SMTWTFS".split("");
                    "three" == f.header_weekdayStyle && (l = "SUN MON TUE WED THU FRI SAT".split(" "));
                    if ("Sunday" == f.start_weekday) {
                        e = '<table class="argTable"><thead><tr class="headerRow">';
                        for (b = 0; b < l.length; b++) e += "<td>" + l[b] + "</td>";
                        e += "</tr><tbody>" + a + "</tbody></table>"
                    }
                    "Monday" == f.start_weekday &&
                        (e = '<table class="argTable"><thead><tr class="headerRow"><td>M</td><td>T</td><td>W</td><td>T</td><td>F</td><td>S</td><td>S</td></tr><tbody>' + a + "</tbody></table>");
                    m.append(e); - 1 < N && (K(), ("auto" == m.css("height") || "0px" == m.css("height")) && m.css("height", 7 * c.find(".argTable tbody").find("tr").eq(0).height()));
                    if (1 != m.children().length) {
                        transitioned = !1;
                        x = m.children(".currTable");
                        s = m.children(".argTable");
                        ("slide" == f.design_transition || "fade" == f.design_transition || "none" == f.design_transition) && x.css({
                            top: 0,
                            left: 0
                        });
                        if ("slide" == f.design_transition && (transitioned = !0, !0 == B ? (x.animate({
                            top: 0,
                            left: -(w + 10)
                        }, {
                            queue: !1,
                            complete: C,
                            duration: settings_dzscalendar.animation_time,
                            easing: settings_dzscalendar.animation_easing
                        }), s.css({
                            top: 0,
                            left: w + 10
                        })) : (x.animate({
                            top: 0,
                            left: w + 10
                        }, {
                            queue: !1,
                            complete: C,
                            duration: settings_dzscalendar.animation_time,
                            easing: settings_dzscalendar.animation_easing
                        }), s.css({
                            top: 0,
                            left: -(w + 10)
                        })), s.animate({
                            top: 0,
                            left: 0
                        }, {
                            queue: !1,
                            duration: settings_dzscalendar.animation_time,
                            easing: settings_dzscalendar.animation_easing
                        }), !is_ie8())) {
                            for (b = s.find("tbody").find("tr").length; - 1 < b; b--) q = s.find("tbody").find("tr").eq(b), q.css({
                                opacity: 0
                            }), a = 3 * settings_dzscalendar.animation_time / (s.find("tbody").find("tr").length - b + 1), q.delay(settings_dzscalendar.animation_time / 2).animate({
                                opacity: 1
                            }, {
                                queue: !0,
                                duration: a,
                                easing: settings_dzscalendar.animation_easing
                            });
                            for (b = s.find("tbody").find("tr").length; - 1 < b;) break
                        }
                        "fade" == f.design_transition && (transitioned = !0, x.animate({
                            opacity: 0
                        }, {
                            queue: !1,
                            complete: C,
                            duration: settings_dzscalendar.animation_time,
                            easing: settings_dzscalendar.animation_easing
                        }), s.css({
                            top: 0,
                            left: 0,
                            opacity: 0
                        }), s.animate({
                            opacity: 1
                        }, {
                            queue: !1,
                            duration: settings_dzscalendar.animation_time,
                            easing: settings_dzscalendar.animation_easing
                        }));
                        !1 == transitioned && C()
                    }
                }
            }

            function C() {
                x.remove();
                A = !1
            }
            var c = jQuery(this),
                J = "",
                z;
            c.children();
            var N = -1,
                t = 0,
                r = 0,
                x, j, s, A = !1,
                B = !1,
                q, w = 182,
                F = 138;
            parseInt(f.settings_slideshowTime);
            var b = 0,
                p = 0,
                m, G, I, g = [],
                u, v, M, E = "auto",
                O = "Enero Febrero Marzo Abril Mayo Junio Julio Agosto Septiembre Octubre Noviembre Diciembre".split(" "),
                J = "string" == typeof c.attr("class") ? c.attr("class") : c.get(0).className; - 1 == J.indexOf("skin-") && c.addClass(f.settings_skin);
            c.hasClass("skin-default") && (f.settings_skin = "skin-default");
            c.hasClass("skin-black") && (f.settings_skin = "skin-black", w = 192, z = F = 158);
            c.hasClass("skin-aurora") && (z = 220);
            "default" == f.design_transitionDesc && (f.design_transitionDesc = "tooltipDef");
            u = new Date;
            for (b = 0; b < c.children(".events").children().length; b++) q = c.children(".events").children().eq(b), g[b] = {
                date: q.attr("data-date"),
                content: q.html(),
                repeat: q.attr("data-repeat"),
                day: q.attr("data-day"),
                month: q.attr("data-month"),
                year: q.attr("data-year"),
                startday: q.attr("data-startday"),
                endday: q.attr("data-endday")
            };
            c.children(".events").remove();
            c.append('<div class="calendar-controls"><div class="arrow-left"></div><div class="curr-date"><span class="curr-month">' + O[u.getMonth()] + '</span><span class="curr-year">' + u.getFullYear() + '</span></div><div class="arrow-right"></div></div>');
            c.append('<div class="theMonths"></div>');
            I = c.find(".curr-date");
            m = c.children(".theMonths");
            G = c.children(".calendar-controls");
            "slide" == f.design_transitionDesc && m.css({
                overflow: "hidden"
            });
            c.find(".arrow-left").click(function () {
                var b = t - 1,
                    c = r; - 1 == b && (b = 11, c--);
                H(c, b)
            });
            c.find(".arrow-right").click(function () {
                var b = t + 1,
                    c = r;
                12 == b && (b = 0, c++);
                H(c, b)
            });
            //D(document).on("click", ".hasEvent", L);
            t = u.getMonth();
            r = u.getFullYear();
            "" != f.start_year && (r = parseInt(f.start_year, 10));
            "" != f.start_month && (t = parseInt(f.start_month, 10), t--);
            H(r, t); - 1 == J.indexOf("responsive") && (c.css("height",
                c.height()), E = c.height());
            return this
        })
    }
})($dzscal);