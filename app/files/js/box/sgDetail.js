const sgDetail = {
    name: 'sgDetail',
    props: ['enable', 'sgInfo', 'isRoomOrBox', 'roomNumber'],
    template:`
    <div class="sgP-wrap">
    <div class="sgP-area">
        <img :src="baseUrl+'close.png'" class="sgP-close" @click="cancelDetail">
        <div class="sgP-title">
            <img :src="baseUrl+'storetitle.png'" alt="房间设置">
            <span>房间设置</span>
        </div>
        <div class="sgP-show">
            <div class="sgP-tips">
                <div class="sgP-tips-before"></div>
                <div class="sgP-tips-text">创建房间，游戏未进行不消耗房卡呦</div>
                <div class="sgP-tips-after"></div>
            </div>
            <div class="sgP-mode">
                <div class="sgP-mode-index" v-for="(item, index) in sgProperty.modes" @click="modeSelect(index)">
                    <img :src="baseUrl+'mode_selected.png'" v-if="banker_mode===index">
                    <img :src="baseUrl+'mode_unselected.png'" v-else>
                    <p class="mode_name">{{item.up}}</p>
                    <p class="mode_name">{{item.down}}</p>
                </div>
            </div>
            <div class="sgP-control">
                <img :src="baseUrl+'settingback.png'">
                <div class="sgP-property">
                    <div class="sgP-score">
                        <div class="sgP-property-title">底分：</div>
                        <div class="sgP-property-area">
                            <div class="bullP-score-item" v-for="(item, index) in sgProperty.scores">
                                <div class="sgP-score-check" @click="scoreSelect(index)">
                                    <img :src="enable ?  baseUrl+'tick.png' : baseUrl+'disabledTick.png' " v-show="score_type===index">
                                </div>
                                <div class="sgP-score-text">
                                    {{item}}分 
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sgP-time">
                        <div class="sgP-property-title">时间：</div>
                        <div class="sgP-property-area">
                            <div class="sgP-time-select" v-for="(item, index) in sgProperty.times">
                                <span>{{item.text}}</span>
                                <select v-model="countDown[index]" :disabled="enable ? false : true">
                                    <option v-for="value in item.select">{{value}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="sgP-rule">
                        <div class="sgP-property-title">规则：</div>
                        <div class="sgP-property-area">
                            <div class="sgP-rule-item" v-for="(item,index) in sgProperty.rules" >
                                <div class="sgP-rule-check" @click="ruleSelect(index)">
                                    <img :src="enable ?  baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-if="rules[index]===true">
                                </div>
                                <div class="sgP-rule-text">
                                    {{item}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bullP-num">
                        <div class="sgP-property-title">局数：</div>
                        <div class="sgP-property-area">
                            <div class="sgP-num-item" v-for="(item, index) in sgProperty.nums" v-if="game_type===36">
                                <div class="sgP-num-check" @click="ticketSelect(index+1)">
                                    <img :src="enable ?  baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-if="ticket_type===(index+1)">
                                </div>
                                <div class="sgP-num-text">
                                    {{item}}
                                </div>
                            </div>
                            <div class="sgP-num-item" v-for="(item, index1) in sgProperty.nineNums" v-if="game_type===37">
                                <div class="sgP-num-check" @click="ticketSelect1(index1+1)">
                                    <img :src="enable ?  baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-if="ticket_type1===(index1+1)">
                                </div>
                                <div class="sgP-num-text">
                                    {{item}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sgP-save" >
                <img :src="baseUrl+'joingame.png'" alt="加入游戏" @click="toJoinGame" v-if="!enable">
                <img :src="baseUrl+'box-save.png'" alt="加入游戏" @click="saveBoxProperty" v-if="enable">
            </div>
        </div>
    </div>
</div>
    `,
    data: function(){
        return {
            sgProperty: sgProperty,
            baseUrl: baseUrl+'files/images/box/',
            banker_mode: 0,
            score_type: 0,
            countDown: [],
            rules: [true,true],
            ticket_type: 1,
            ticket_type1: 1,
            game_type: 0,
            box_number: 0,
            personId: '',
            box_name: '',
            box_id: ''
        }
    },
    beforeMount:function(){
        if(this.enable){
            var info = JSON.parse(this.sgInfo.config);
            this.banker_mode =  info.banker_mode - 1;
            this.score_type = this.sgProperty.scores.indexOf(info.score_type);
            this.countDown = info.countDown;
            this.rules.splice(0, 1, (info.is_joker === 1 ? true : false));
            this.rules.splice(1, 1, (info.is_bj === 1 ? true : false));
            this.ticket_type = info.ticket_type;
            this.ticket_type1 = info.ticket_type;
            this.game_type = info.game_type;
            this.box_number = info.box_number;
            this.personId = this.sgInfo.account_id;
            this.box_name = this.sgInfo.box_name;
            this.box_id = this.sgInfo.box_id;
        }else{
            var info = JSON.parse(this.sgInfo.data.config);
            this.banker_mode =  info.banker_mode - 1;
            this.score_type = this.sgProperty.scores.indexOf(info.score_type);
            this.countDown = info.countDown;
            this.rules.splice(0, 1, (info.is_joker === 1 ? true : false));
            this.rules.splice(1, 1, (info.is_bj === 1 ? true : false));
            this.ticket_type = info.ticket_type;
            this.game_type = info.game_type;
            this.box_number = info.box_number;
            this.personId = this.sgInfo.account_id;
        }
    },
    methods: {
        // 模式选择
        modeSelect(index){
            if(this.enable){
                this.banker_mode = index;
            }
        },
        // 底分选择
        scoreSelect(index){
            if(this.enable){
                this.score_type = index;
            }
        },
        // 规则选择
        ruleSelect(index){
            if(this.enable){
                this.rules.splice(index, 1, !this.rules[index]);
            }
        },
        ticketSelect(index){
            if(this.enable){
                this.ticket_type = index;
            }
        },
        ticketSelect1(index){
            if(this.enable){
                this.ticket_type1 = index;
            }
        },
        cancelDetail(){
            this.$emit('cancelDetail');
        },
        saveBoxProperty(){
            var _this = this;
            var postData = {
                account_id: accountId,
                box_name: this.box_name,
                box_id:this.box_id,
                game_type: this.game_type,
                data: {
                    data_key:Date.parse(new Date())+randomString(5),
                    banker_mode: this.banker_mode + 1,
                    score_type: this.sgProperty.scores[this.score_type],
                    countDown: (function(){
                        return _this.countDown.map(function(value, index) {
                            return Number(value);
                        })
                    })(),
                    is_joker: this.rules[0] ? 1 : 0,
                    is_bj: this.rules[1] ? 1 : 0,
                    ticket_type: (this.game_type === 36) ? (this.ticket_type) : (this.ticket_type1),
                    game_type: this.game_type,
                }
            }
            this.$http.post('/box/setBoxInfo', JSON.stringify(postData)).then(function(returnValue){
                var result = returnValue.data.result;
                if(result === 0){
                    console.log('修改成功');
                    this.$emit('checkData', postData.data);
                }
            })
        },
        toJoinGame(){
            if(this.isRoomOrBox===0){
                var _this = this;
                var url = returnGameWsUrl(this.game_type);
                var wsObj = {
                    operation: 'JoinBox',
                    account_id: accountId,
                    session: session,
                    data: {
                        box_number: this.box_number,
                        game_type: this.game_type
                    }
                }
                var is_opearation = true;
                var socketStatus = 0;
                var ws  = new WebSocket(url);
                ws.onopen = function(evt){
                    console.log('已连接');
                    var tiao=setInterval(function(){
                        socketStatus=socketStatus+1;
                        ws.send("@");
                        if(socketStatus>3||socketStatus>3){
                            window.location.reload();
                        }
                    },4000);
                    ws.send(JSON.stringify(wsObj));
                }
                ws.onmessage = function(evt){
                    if(evt.data=="@"){
                        socketStatus=0;
                        return 0;
                    }
                    var obj = JSON.parse(evt.data);
                    if(Number(obj.result) === 1){
                        _this.$emit('showAl', obj.result_message);
                        return;
                    }else if(Number(obj.result) === 0){
                        if(Number(_this.game_type) === 5){
                            window.location.href = baseUrl + 'f/b?i=' + obj.data.room_number + '_';
                        }else if(Number(_this.game_type) === 9){
                            window.location.href = baseUrl + 'f/nb?i=' + obj.data.room_number + '_';
                        }else if(Number(_this.game_type) === 71){
                            window.location.href = baseUrl + 'f/lb?i=' + obj.data.room_number + '_';
                        }else if(Number(_this.game_type) === 12){
                            window.location.href = baseUrl + 'f/tb?i=' + obj.data.room_number + '_';
                        }else if(Number(_this.game_type) === 13){
                            window.location.href = baseUrl + 'f/fb?i=' + obj.data.room_number + '_';                   
                        }else if(Number(_this.game_type) === 36){
                            window.location.href = baseUrl + 'f/sg?i=' + obj.data.room_number + '_';
                        }else if(Number(_this.game_type) === 37){
                            window.location.href = baseUrl + 'f/nsg?i=' + obj.data.room_number + '_';
                        }else if(Number(_this.game_type) === 1){
                            window.location.href = baseUrl + 'f/yf?i=' + obj.data.room_number + '_';            
                        }else if(Number(_this.game_type) === 110){
                            window.location.href = baseUrl + 'f/tf?i=' + obj.data.room_number + '_';
                        }else if(Number(_this.game_type) === 111){
                            window.location.href = baseUrl + 'f/bf?i=' + obj.data.room_number + '_';
                        }else if(Number(_this.game_type) === 61){
                            window.location.href = baseUrl + 'f/dp?i=' + obj.data.room_number + '_';
                        }
                    } else if (Number(obj.result) === -201){
                        is_operation=false;
                        console.log('出错了');
                    }  else {
                        is_operation=false;
                        console.log('出错');
                    }
                }
                ws.onclose = function(evt) {
                    if(is_opearation){
                        toJoinGame();
                    }else {
                        return 0;
                    }
                }
                ws.onerror = function(err){
                    console.log('出错了');
                }
            }else if(this.isRoomOrBox===1){
                console.log(111111111111111111);
                if(Number(this.game_type) === 5){
                    window.location.href = baseUrl + 'f/b?i=' + this.roomNumber + '_';
                }else if(Number(this.game_type) === 9){
                    window.location.href = baseUrl + 'f/nb?i=' + this.roomNumber + '_';
                }else if(Number(this.game_type) === 71){
                    window.location.href = baseUrl + 'f/lb?i=' + this.roomNumber + '_';
                }else if(Number(this.game_type) === 12){
                    window.location.href = baseUrl + 'f/tb?i=' + this.roomNumber + '_';
                }else if(Number(this.game_type) === 13){
                    window.location.href = baseUrl + 'f/fb?i=' + this.roomNumber + '_';                   
                }else if(Number(this.game_type) === 36){
                    window.location.href = baseUrl + 'f/sg?i=' + this.roomNumber + '_';
                }else if(Number(this.game_type) === 37){
                    window.location.href = baseUrl + 'f/nsg?i=' + this.roomNumber + '_';
                }else if(Number(this.game_type) === 1){
                    window.location.href = baseUrl + 'f/yf?i=' + this.roomNumber + '_';            
                }else if(Number(this.game_type) === 110){
                    window.location.href = baseUrl + 'f/tf?i=' + this.roomNumber + '_';
                }else if(Number(this.game_type) === 111){
                    window.location.href = baseUrl + 'f/bf?i=' + this.roomNumber + '_';
                }else if(Number(this.game_type) === 61){
                    window.location.href = baseUrl + 'f/dp?i=' + this.roomNumber + '_';
                }
            }
        }
    }
}