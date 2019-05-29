(function($) {
    $.fn.timepicki = function(options) {
        var defaults = {};
        var settings = $.extend({},
        defaults, options);
        return this.each(function() {
            var ele = $(this);
            var ele_hei = ele.outerHeight();
            var ele_lef = ele.position().left;
            ele_hei += 15;
            $(ele).wrap("<div class='time_pick'>");
            var ele_par = $(this).parents(".time_pick");
            ele_par.append("<div class='timepicker_wrap'><div class='arrow_top'></div><div style='height:35px;'><div class='timeTitle'>起始时间</div><div class='timeTitle'>结束时间</div></div><div class='time'><div class='prevD'></div><div class='ti_tx ti_tx1'></div><div class='nextD'></div></div><div class='mins' style='margin-right:30px;'><div class='prevD'></div><div class='mi_tx mi_tx1'></div><div class='nextD'></div></div><div class='time1'><div class='prevD'></div><div class='ti_tx ti_tx2'></div><div class='nextD'></div></div><div class='mins1'><div class='prevD'></div><div class='mi_tx mi_tx2'></div><div class='nextD'></div></div></div>");
            var ele_next = $(this).next(".timepicker_wrap");
            var ele_next_all_child = ele_next.find("div");
            ele_next.css({
                "top": ele_hei + "px",
                "left": ele_lef + "px"
            });
            $(document).on("click",
            function(event) {
                if (!$(event.target).is(ele_next)) {
                    if (!$(event.target).is(ele)) {
                        var tim1 = ele_next.find(".ti_tx1").html();
                        var mini1 = ele_next.find(".mi_tx1").text();
                        var tim2 = ele_next.find(".ti_tx2").html();
                        var mini2 = ele_next.find(".mi_tx2").text();
                        if (tim1.length != 0 && mini2.length != 0&&tim2.length != 0 && mini2.length != 0 ) {
                            ele.val(tim1 + ":" + mini1 + "~" + tim2 + ":" + mini2)
                        }
                        if (!$(event.target).is(ele_next) && !$(event.target).is(ele_next_all_child)) {
                            ele_next.fadeOut()
                        }
                    } else {
                        set_date();
                        ele_next.fadeIn()
                    }
                }
            });
            function set_date() {
            	var start_time=$("#timePick").val().substr(0,5);
				var end_time=$("#timePick").val().substr(6,5);
				
				 ele_next.find(".ti_tx1").text($("#timePick").val().substr(0,2))
				 ele_next.find(".mi_tx1").text($("#timePick").val().substr(3,2))				
				 ele_next.find(".ti_tx2").text($("#timePick").val().substr(6,2))
				 ele_next.find(".mi_tx2").text($("#timePick").val().substr(9,2))
				
        /*        var d = new Date();
                var ti = d.getHours();
                var mi = d.getMinutes();

                if (ti < 10) {
                    ele_next.find(".ti_tx").text("0" + ti)
                } else {
                    ele_next.find(".ti_tx").text(ti)
                }
                if (mi < 10) {
                    ele_next.find(".mi_tx").text("0" + mi)
                } else {
                    ele_next.find(".mi_tx").text(mi)
                }*/
            }
            var cur_next = ele_next.find(".nextD");
            var cur_prev = ele_next.find(".prevD");
            $(cur_prev).add(cur_next).on("click",
            function() {
           
                var cur_ele = $(this);
                var cur_cli = null;
                var ele_st = 0;
                var ele_en = 0;
                if (cur_ele.parent().attr("class") == "time"){
                    cur_cli = "time";
                    ele_en = 23;
                    var cur_time = null;
                    cur_time = ele_next.find("." + cur_cli + " .ti_tx").text();
                    cur_time = parseInt(cur_time);
                    if (cur_ele.attr("class") == "nextD") {
                        if (cur_time == 23) {
                            ele_next.find("." + cur_cli + " .ti_tx").text("00")
                        } 
                        else {
                            cur_time++;
                            if (cur_time < 10) {
                                ele_next.find("." + cur_cli + " .ti_tx").text("0" + cur_time)
                            } else {
                                ele_next.find("." + cur_cli + " .ti_tx").text(cur_time)
                            }
                        }
                    } else {
                        if (cur_time == 0) {
                            ele_next.find("." + cur_cli + " .ti_tx").text(23)
                        } else {
                            cur_time--;
                            if (cur_time < 10) {
                                ele_next.find("." + cur_cli + " .ti_tx").text("0" + cur_time)
                            } else {
                                ele_next.find("." + cur_cli + " .ti_tx").text(cur_time)
                            }
                        }
                    }
                }
                else if (cur_ele.parent().attr("class") == "time1"){
                    cur_cli = "time1";
                    ele_en = 23;
                    var cur_time = null;
                    cur_time = ele_next.find("." + cur_cli + " .ti_tx").text();
                    cur_time = parseInt(cur_time);
                    if (cur_ele.attr("class") == "nextD") {
                        if (cur_time == 23) {
                            ele_next.find("." + cur_cli + " .ti_tx").text("00")
                        } 
                        else {
                            cur_time++;
                            if (cur_time < 10) {
                                ele_next.find("." + cur_cli + " .ti_tx").text("0" + cur_time)
                            } else {
                                ele_next.find("." + cur_cli + " .ti_tx").text(cur_time)
                            }
                        }
                    } else {
                        if (cur_time == 0) {
                            ele_next.find("." + cur_cli + " .ti_tx").text(23)
                        } else {
                            cur_time--;
                            if (cur_time < 10) {
                                ele_next.find("." + cur_cli + " .ti_tx").text("0" + cur_time)
                            } else {
                                ele_next.find("." + cur_cli + " .ti_tx").text(cur_time)
                            }
                        }
                    }
                } 
                else if (cur_ele.parent().attr("class") == "mins"){
                    cur_cli = "mins";
                    ele_en = 59;
                    var cur_mins = null;
                    cur_mins = ele_next.find("." + cur_cli + " .mi_tx").text();
                    cur_mins = parseInt(cur_mins);
                    if (cur_ele.attr("class") == "nextD") {
                        if (cur_mins == 59) {
                            ele_next.find("." + cur_cli + " .mi_tx").text("00")
                        } else {
                            cur_mins++;
                            if (cur_mins < 10) {
                                ele_next.find("." + cur_cli + " .mi_tx").text("0" + cur_mins)
                            } else {
                                ele_next.find("." + cur_cli + " .mi_tx").text(cur_mins)
                            }
                        }
                    } else {
                        if (cur_mins == 0) {
                            ele_next.find("." + cur_cli + " .mi_tx").text(59)
                        } else {
                            cur_mins--;
                            if (cur_mins < 10) {
                                ele_next.find("." + cur_cli + " .mi_tx").text("0" + cur_mins)
                            } else {
                                ele_next.find("." + cur_cli + " .mi_tx").text(cur_mins)
                            }
                        }
                    }
                }
                else if (cur_ele.parent().attr("class") == "mins1"){
                    cur_cli = "mins1";
                    ele_en = 59;
                    var cur_mins = null;
                    cur_mins = ele_next.find("." + cur_cli + " .mi_tx").text();
                    cur_mins = parseInt(cur_mins);
                    if (cur_ele.attr("class") == "nextD") {
                        if (cur_mins == 59) {
                            ele_next.find("." + cur_cli + " .mi_tx").text("00")
                        } else {
                            cur_mins++;
                            if (cur_mins < 10) {
                                ele_next.find("." + cur_cli + " .mi_tx").text("0" + cur_mins)
                            } else {
                                ele_next.find("." + cur_cli + " .mi_tx").text(cur_mins)
                            }
                        }
                    } else {
                        if (cur_mins == 0) {
                            ele_next.find("." + cur_cli + " .mi_tx").text(59)
                        } else {
                            cur_mins--;
                            if (cur_mins < 10) {
                                ele_next.find("." + cur_cli + " .mi_tx").text("0" + cur_mins)
                            } else {
                                ele_next.find("." + cur_cli + " .mi_tx").text(cur_mins)
                            }
                        }
                    }
                }
               
            })
        })
    }
} (jQuery));

