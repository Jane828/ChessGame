var bullDetail = {
    name: 'bullDetail',
    props: ['enable', 'bullInfo', 'isRoomOrBox', 'roomNumber'],
    template: `
    <div class="bullP-wrap">
    <div class="bullP-area">
        <img :src="baseUrl+'close.png'" class="bullP-close" @click="cancelDetail">
        <div class="bullP-title">
            <img :src="baseUrl+'storetitle.png'" alt="房间设置">
            <span>房间设置</span>
        </div>
        <div class="bullP-show">
            <div class="bullP-tips">
                <div class="bullP-tips-before"></div>
                <div class="bullP-tips-text">创建房间，游戏未进行不消耗房卡呦</div>
                <div class="bullP-tips-after"></div>
            </div>
            <div class="bullP-mode">
                <div class="bullP-mode-index" v-for="(item, index) in bullProperty.modes" @click="bankerSelect(index)">
                    <img :src="baseUrl+'mode_selected.png'"  v-if="banker_mode===index">
                    <img :src="baseUrl+'mode_unselected.png'" v-else>
                    <p class="mode_name">{{item.up}}</p>
                    <p class="mode_name">{{item.down}}</p>
                </div>
            </div>
            <div class="bullP-control">
                <img :src="baseUrl+'settingback.png'">
                <div class="bullP-property">
                    <div class="bullP-score">
                        <div class="bullP-property-title">底分：</div>
                        <div class="bullP-property-area">
                            <div class="bullP-score-item" v-for="(item, index) in bullProperty.tongScores" v-show="banker_mode===3">
                                <div class="bullP-score-check"  @click="scoreSelect(index)">
                                    <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-if="score_type===index">
                                </div>
                                <div class="bullP-score-text">
                                    {{(index===3) ? item : (item+'分')}}
                                    <select  v-show="index===3" v-model="score_value" :disabled="scoreList||!enable">
                                        <option v-for="num in bullProperty.scoreList()">{{num}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="bullP-score-item" v-for="(item, index1) in bullProperty.scores" v-show="banker_mode!==3">
                                <div class="bullP-score-check" @click="scoreSelect1(index1)">
                                    <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'"  v-if="score_type1===index1" >
                                </div>
                                <div class="bullP-score-text">
                                    {{(index1===4) ? item : (item+'分')}}
                                    <select v-show="index1===4" v-model="score_value1" :disabled="scoreList1||!enable">
                                        <option v-for="num in bullProperty.scoreList()">{{num}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bullP-time">
                        <div class="bullP-property-title">时间：</div>
                        <div class="bullP-property-area">
                            <div class="bullP-time-select" v-for="(item, index) in bullProperty.times">
                                <span>{{item.text}}</span>
                                <select v-model="countDown[index]" :disabled="!enable">
                                    <option v-for="value in item.select">{{value}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="bullP-rule">
                        <div class="bullP-property-title">规则：</div>
                        <div class="bullP-property-area">
                            <div class="bullP-rule-item" v-for="(item,index) in bullProperty.rules">
                                <div class="bullP-rule-check" @click="ruleSelect(index)">
                                    <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-if="rule_type===index">
                                </div>
                                <div class="bullP-rule-text">
                                    {{item}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bullP-porker">
                        <div class="bullP-property-title">牌型：</div>
                        <div class="bullP-property-area">
                            <div class="bullP-porker-item" v-for="(item, index) in bullProperty.porkers" v-if="game_type!==5 && game_type!==9">
                                <div class="bullP-porker-check" @click="porkerSelect(index)">
                                <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-if="porker[index]">
                                </div>
                                <div class="bullP-porker-text">
                                    {{item}}
                                </div>
                            </div>
                            <div class="bullP-porker-item" v-for="(item, index1) in bullProperty.lporkers" v-if="game_type===5 || game_type===9">
                                <div class="bullP-porker-check" @click="porkerSelect1(index1)">
                                <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-if="porker1[index1]">
                                </div>
                                <div class="bullP-porker-text">
                                    {{item}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bullP-num">
                        <div class="bullP-property-title">局数：</div>
                        <div class="bullP-property-area">
                            <div class="bullP-num-item" v-for="(item, index) in bullProperty.nums" v-if="game_type!==5">
                                <div class="bullP-num-check" @click="ticketSelect(index)">
                                    <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-if="ticket_type===index">
                                </div>
                                <div class="bullP-num-text">
                                    {{item}}
                                </div>
                            </div>
                            <div class="bullP-num-item" v-for="(item, index1) in bullProperty.bullNums" v-if="game_type===5">
                                <div class="bullP-num-check" @click="ticketSelect1(index1)">
                                    <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-if="ticket_type1===index1">
                                </div>
                                <div class="bullP-num-text">
                                    {{item}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bullP-multiple">
                        <div class="bullP-property-title">倍数：</div>
                        <div class="bullP-property-area">
                            <div class="bullP-multiple-item" v-for="(item, index) in bullProperty.multiples">
                                <div class="bullP-multiple-check" @click="timesSelect(index)">
                                <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'"  v-if="times_type===index">
                                </div>
                                <div class="bullP-multiple-text">
                                    {{item}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bullP-up" v-if="banker_mode===4">
                        <div class="bullP-property-title">上庄：</div>
                        <div class="bullP-property-area">
                            <div class="bullP-up-item" v-for="(item, index) in bullProperty.ups">
                                <div class="bullP-up-check" @click="upSelect(index)">
                                <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-if="banker_score_type===index">
                                </div>
                                <div class="bullP-up-text">
                                    {{item}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bullP-save">
                <img :src="baseUrl+'joingame.png'" alt="加入游戏" @click="toJoinGame" v-if="!enable">
                <img :src="baseUrl+'box-save.png'" alt="保存" @click="saveBoxProperty" v-if="enable">
            </div>
        </div>
    </div>
</div>
    `,
    data: function(){
        return {
            baseUrl: baseUrl+'files/images/box/',
            banker_mode: 0,
            score_type: 0,
            score_type1: 0,
            countDown: [],
            rule_type: 0,
            porker: [true, true, true, true, true, true, true, true],
            porker1: [true, true, true, true, true, true, true, true, true],
            ticket_type: 0,
            ticket_type1: 0,
            times_type: 0,
            banker_score_type: 0,
            score_value: 0,
            score_value1: 0,
            scoreList: true,
            scoreList1: true,
            box_number: 0,
            personId: '',
            box_name: '',
            box_id: ''
        }
    },
    beforeMount(){
        if(this.enable){
            var _this = this;
            var info = JSON.parse(this.bullInfo.config);
            this.banker_mode = info.banker_mode - 1;
            if(this.banker_mode !== 3){
                var ins = this.selectScoreType(info.score_type);
                if(ins !== -1){
                    this.score_type1 = ins;
                }
            }else {
                var ins = this.selectScoreType1(info.score_type);
                if(ins !== -1){
                    this.score_type = ins;
                }
            };
            this.countDown = info.countDown;
            this.rule_type = info.rule_type - 1;
            this.game_type = info.game_type;
            if(this.game_type === 5 || this.game_type === 9){
                this.$set(this.porker1, 0, (info.has_ghost===1 ? true : false));
                this.$set(this.porker1, 1, (info.is_cardfour===1 ? true : false));
                this.$set(this.porker1, 2, (info.is_cardfive===1 ? true : false));
                this.$set(this.porker1, 3, (info.is_straight===1 ? true : false));
                this.$set(this.porker1, 4, (info.is_flush===1 ? true : false));
                this.$set(this.porker1, 5, (info.is_hulu===1 ? true : false));
                this.$set(this.porker1, 6, (info.is_cardbomb===1 ? true : false));
                this.$set(this.porker1, 7, (info.is_straightflush===1 ? true : false));
                this.$set(this.porker1, 8, (info.is_cardtiny===1 ? true : false));
            }else {
                this.$set(this.porker, 0, (info.is_cardfour===1 ? true : false));
                this.$set(this.porker, 1, (info.is_cardfive===1 ? true : false));
                this.$set(this.porker, 2, (info.is_straight===1 ? true : false));
                this.$set(this.porker, 3, (info.is_flush===1 ? true : false));
                this.$set(this.porker, 4, (info.is_hulu===1 ? true : false));
                this.$set(this.porker, 5, (info.is_cardbomb===1 ? true : false));
                this.$set(this.porker, 6, (info.is_straightflush===1 ? true : false));
                this.$set(this.porker, 7, (info.is_cardtiny===1 ? true : false));
            }
            this.ticket_type = info.ticket_type-1;
            this.ticket_type1 = info.ticket_type - 1;
            this.times_type = info.times_type - 1;
            this.banker_score_type = info.banker_score_type - 1;
            if(this.banker_mode === 3){
                this.score_value = info.score_value
            }else{
                this.score_value1 = info.score_value;
            }
            this.box_number = info.box_number;
            this.personId = this.bullInfo.account_id;
            this.box_name = this.bullInfo.box_name;
            this.box_id = this.bullInfo.box_id;
        }else{
            var _this = this;
            var info = JSON.parse(this.bullInfo.data.config);
            this.banker_mode = info.banker_mode - 1;
            if(this.banker_mode !== 3){
                var ins = this.selectScoreType(info.score_type);
                if(ins !== -1){
                    this.score_type1 = ins;
                }
            }else {
                var ins = this.selectScoreType1(info.score_type);
                if(ins !== -1){
                    this.score_type = ins;
                }
            };
            this.countDown = info.countDown;
            this.rule_type = info.rule_type - 1;
            this.game_type = info.game_type;
            if(this.game_type === 5 || this.game_type === 9){
                this.$set(this.porker1, 0, (info.has_ghost===1 ? true : false));
                this.$set(this.porker1, 1, (info.is_cardfour===1 ? true : false));
                this.$set(this.porker1, 2, (info.is_cardfive===1 ? true : false));
                this.$set(this.porker1, 3, (info.is_straight===1 ? true : false));
                this.$set(this.porker1, 4, (info.is_flush===1 ? true : false));
                this.$set(this.porker1, 5, (info.is_hulu===1 ? true : false));
                this.$set(this.porker1, 6, (info.is_cardbomb===1 ? true : false));
                this.$set(this.porker1, 7, (info.is_straightflush===1 ? true : false));
                this.$set(this.porker1, 8, (info.is_cardtiny===1 ? true : false));
            }else {
                this.$set(this.porker, 0, (info.is_cardfour===1 ? true : false));
                this.$set(this.porker, 1, (info.is_cardfive===1 ? true : false));
                this.$set(this.porker, 2, (info.is_straight===1 ? true : false));
                this.$set(this.porker, 3, (info.is_flush===1 ? true : false));
                this.$set(this.porker, 4, (info.is_hulu===1 ? true : false));
                this.$set(this.porker, 5, (info.is_cardbomb===1 ? true : false));
                this.$set(this.porker, 6, (info.is_straightflush===1 ? true : false));
                this.$set(this.porker, 7, (info.is_cardtiny===1 ? true : false));
            }
            this.ticket_type = info.ticket_type-1;
            this.ticket_type1 = info.ticket_type - 1;
            this.times_type = info.times_type - 1;
            this.banker_score_type = info.banker_score_type - 1;
            if(this.banker_mode === 3){
                this.score_value = info.score_value
            }else{
                this.score_value1 = info.score_value;
            }
            this.box_number = info.box_number;
            this.personId = this.bullInfo.account_id;
        }
    },
    methods: {
        cancelDetail(){
            this.$emit('cancelDetail');
        },
        selectScoreType(score_type){
            var arr = [1,6,2,3,7];
            return arr.indexOf(score_type);
        },
        selectScoreType1(score_type){
            var arr = [3, 4, 5, 7];
            return arr.indexOf(score_type);
        },
        bankerSelect(index){
            if(this.enable){
                this.banker_mode = index;
            }
        },
        scoreSelect(index){
            if(this.enable){
                this.scoreList = (index===3) ? false : true;
                this.score_type = index;
            }
        },
        scoreSelect1(index){
            if(this.enable){
                this.scoreList1 = (index==4) ? false : true;
                this.score_type1 = index;
            }
        },
        ruleSelect(index){
            if(this.enable){
                this.rule_type = index;
            }
        },
        porkerSelect(index){
            if(this.enable){
                this.$set(this.porker, index, !this.porker[index]);
            }
        },
        porkerSelect1(index){
            if(this.enable){
                this.$set(this.porker1, index, !this.porker1[index]);
            }
        },
        ticketSelect(index){
            if(this.enable){
                this.ticket_type = index;
            }
        },
        ticketSelect1(index){
            if(this.enable){
                console.log(22222222)
                this.ticket_type1 = index;
            }
        },
        timesSelect(index){
            if(this.enable){
                console.log(1111)
                this.times_type = index;
            }
        },
        upSelect(index){
            if(this.enable){
                this.banker_score_type = index;
            }
        },
        selectType1(scoreType){
            var arr = [1,6,2,3,7];
            return arr[scoreType];
        },
        selectType(scoreType){
            var arr = [3, 4, 5, 7];
            return arr[scoreType];
        },
        saveBoxProperty(){
            var _this = this;
            var postData = {
                account_id: accountId,
                game_type: this.game_type,
                box_name: this.box_name,
                box_id: this.box_id,
                data: {
                    data_key:Date.parse(new Date())+randomString(5),
                    banker_mode: this.banker_mode + 1,
                    score_type: (_this.banker_mode===3) ? this.selectType(this.score_type) : this.selectType1(this.score_type1),
                    game_type: this.game_type,
                    countDown: (function(){
                        return _this.countDown.map(function(value, index) {
                            return Number(value);
                        })
                    })(),
                    rule_type: this.rule_type+1,
                    ticket_type: (this.game_type === 5) ? (this.ticket_type1+1) : (this.ticket_type+1),
                    times_type: this.times_type + 1,
                    is_cardfour: ((this.game_type === 5) || (this.game_type === 9)) ? (this.porker1[1] ? 1 : 0) : (this.porker[0] ? 1 : 0),
                    is_cardfive: ((this.game_type === 5) || (this.game_type === 9)) ? (this.porker1[2] ? 1 : 0) : (this.porker[1] ? 1 : 0),
                    is_straight: ((this.game_type === 5) || (this.game_type === 9)) ? (this.porker1[3] ? 1 : 0) : (this.porker[2] ? 1 : 0),
                    is_flush: ((this.game_type === 5) || (this.game_type === 9)) ? (this.porker1[4] ? 1 : 0) : (this.porker[3] ? 1 : 0),
                    is_hulu: ((this.game_type === 5) || (this.game_type === 9)) ? (this.porker1[5] ? 1 : 0) : (this.porker[4] ? 1 : 0),
                    is_cardbomb: ((this.game_type === 5) || (this.game_type === 9)) ? (this.porker1[6] ? 1 : 0) : (this.porker[5] ? 1 : 0),
                    is_straightflush: ((this.game_type === 5) || (this.game_type === 9)) ? (this.porker1[7] ? 1 : 0) : (this.porker[6] ? 1 : 0),
                    is_cardtiny: ((this.game_type === 5) || (this.game_type === 9)) ? (this.porker1[8] ? 1 : 0) : (this.porker[7] ? 1 : 0),
                    banker_score_type: this.banker_score_type + 1,
                    score_value: (this.banker_mode===3) ? this.score_value : this.score_value1,
                }
            }
            if(this.game_type===5||this.game_type===9){
                postData.data.has_ghost = (this.porker1[0] ? 1 : 0);
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
                        game_type: this.game_type,
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