var anbaoDetail = {
    name: 'anbaoDetail',
    props: ['anbaoInfo', 'enable', 'isRoomOrBox', 'roomNumber'],
    template:  `
               <div class="anbaoP-wrap">
                   <div class="anbaoP-area">
                       <img :src="baseUrl+'close.png'" class="anbaoP-close" @click="cancelDetail">
                       <div class="anbaoP-title">
                           <img :src="baseUrl+'storetitle.png'" alt="房间设置">
                           <span>房间设置</span>
                       </div>
                       <div class="anbaoP-show">
                           <div class="anbaoP-tips">
                               <div class="anbaoP-tips-before"></div>
                               <div class="anbaoP-tips-text">创建房间，游戏未进行不消耗房卡呦</div>
                               <div class="anbaoP-tips-after"></div>
                           </div>
                           <div class="anbaoP-mode">
                               <div class="anbaoP-mode-index" v-for="(item, index) in anbaoProperty.modes" @click="modeSelect(index)">
                                   <img :src="baseUrl+'mode_selected.png'" v-if="banker_mode===index">
                                   <img :src="baseUrl+'mode_unselected.png'" v-else>
                                   <p class="mode_name">{{item.up}}</p>
                                   <p class="mode_name">{{item.down}}</p>
                               </div>
                           </div>
                           <div class="anbaoP-control">
                               <img :src="baseUrl+'settingback.png'">
                               <div class="anbaoP-property">
                                   <div class="anbaoP-score">
                                       <div class="anbaoP-property-title">筹码：</div>
                                       <div class="anbaoP-score-item" >
                                           <div class="anbaoP-score-check">
                                               <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'"/>
                                           </div>
                                           <div class="anbaoP-score-text" >
                                               10、20、30、50、100
                                           </div>
                                       </div>
                                   </div>
                                   <div class="anbaoP-up">
                                       <div class="anbaoP-up-title">上限：</div>
                                       <div class="anbaoP-score-property">
                                           <div class="anbaoP-seen-range">
                                               <img :src="enable ? baseUrl+'reduce.png' : baseUrl+'disabledreduce.png'" class="seen-select-reduce" @click="reduceLimitValue">
                                               <span class="seen-range-value">{{(Number(limitValue) === 0) ? '无上限' : limitValue}}</span>
                                               <input :disabled="!enable" type="range" min="0" v-model="limitValue" :max="anbaoProperty.upLimit" step="100" class="seen-range" :style="{backgroundSize: limitValue/20 + '% 100%',}">
                                               <img :src="enable ? baseUrl+'add.png' : baseUrl+'disabledadd.png'" class="seen-select-add" @click="addLimitValue">
                                           </div>
                                       </div>
                                   </div>
                                   <div class="anbaoP-odds">
                                       <div class="anbaoP-property-title">赔率：</div>
                                       <br/>
                                       <div class="anbaoP-odds-property" >
                                           <div class="anbaoP-score-item">
                                               <p>龙、虎</p>
                                               <p>出、入</p>
                                           </div>
                                           <div class="anbaoP-odds-item" v-for="(item, index) in anbaoProperty.first_lossrate">
                                               <div class="anbaoP-odds-check" @click="Lossrate_1(index)">
                                                   <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-show="first_lossrate===index">
                                               </div>
                                               <div class="anbaoP-odds-text">
                                                   1:{{item}}
                                               </div>
                                           </div>
                                       </div>
                                       <br/>
                                       <div class="anbaoP-odds-property" >
                                           <div class="anbaoP-score-item">
                                               <p>同、粘</p>
                                           </div>
                                           <div class="anbaoP-odds-item" v-for="(item, index) in anbaoProperty.second_lossrate">
                                               <div class="anbaoP-odds-check" @click="Lossrate_2(index)">
                                                   <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-show="second_lossrate===index">
                                               </div>
                                               <div class="anbaoP-odds-text">
                                                   1:{{item}}
                                               </div>
                                           </div>
                                       </div>
                                       <br/>
                                       <div class="anbaoP-odds-property" >
                                           <div class="anbaoP-score-item">
                                               <p>角、串</p>
                                           </div>
                                           <div class="anbaoP-odds-item" v-for="(item, index) in anbaoProperty.three_lossrate">
                                               <div class="anbaoP-odds-check" @click="Lossrate_3(index)">
                                                   <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-show="three_lossrate===index">
                                               </div>
                                               <div class="anbaoP-odds-text">
                                                   1:{{item}}
                                               </div>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="anbaoP-time">
                                       <div class="anbaoP-property-title">时间：</div>
                                       <div class="anbaoP-property-area">
                                           <div class="anbaoP-time-select" v-for="(item, index) in anbaoProperty.times">
                                               <span>{{item.text}}</span>
                                               <select v-model="countDown[index]" :disabled="!enable">
                                                   <option v-for="value in item.select">{{value}}</option>
                                               </select>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="anbaoP-num">
                                       <div class="anbaoP-property-title">局数：</div>
                                       <div class="anbaoP-num-item" v-for="(item, index) in anbaoProperty.boardNum">
                                           <div class="anbaoP-odds-check" @click="ticketSelect(index)">
                                               <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-show="ticket_count===index">
                                           </div>
                                           <div class="anbaoP-odds-text">
                                               {{item}}
                                           </div>
                                       </div>
                                   </div>
                                   </div>
                               </div>
                           <div class="anbaoP-save" >
                               <img :src="baseUrl+'joingame.png'" alt="加入游戏" @click="toJoinGame" v-if="!enable"> 
                               <img :src="baseUrl+'box-save.png'" alt="加入游戏" @click="saveBoxProperty" v-if="enable">
                           </div>
                       </div>
                   </div>
               </div>
               `,
    data: function(){
        return {
            anbaoProperty: anbaoProperty,
            banker_mode: 0,
            chip_type:[10,20,30,50,100],
            limitValue:600,
            ticket_count:0,
            first_lossrate: 0,
            second_lossrate:0,
            three_lossrate: 0,
            countDown: [30,30,10,30,10],
            baseUrl: baseUrl+'files/images/box/',
            game_type: 0,
            box_number: 0,
            personId: '',
            box_name: '',
            box_id: ''
        }
    },
    beforeMount:function(){
        if(this.enable){
            var info = JSON.parse(this.anbaoInfo.config);
            this.banker_mode =  info.banker_mode - 1;
            this.limitValue = info.upper_limit;
            this.countDown = info.countDown;
            this.chip_type = info.chip_type;
            this.game_type = info.game_type;
            if(info.game_type===61){
                this.ticket_count = (info.ticket_count===2) ? 0 : 1;
            }
            this.first_lossrate = this.anbaoProperty.first_lossrate.indexOf(info.first_lossrate);
            this.second_lossrate = this.anbaoProperty.second_lossrate.indexOf(info.second_lossrate);
            this.three_lossrate = this.anbaoProperty.three_lossrate.indexOf(info.three_lossrate);
            this.box_number = info.box_number;
            this.personId = this.anbaoInfo.account_id;
            this.box_name = this.anbaoInfo.box_name;
            this.box_id = this.anbaoInfo.box_id;
        }else{
            var info = JSON.parse(this.anbaoInfo.data.config);
            this.banker_mode =  info.banker_mode - 1;
            this.limitValue = info.upper_limit;
            this.countDown = info.countDown;
            this.game_type = info.game_type;
            this.chip_type = info.chip_type;
            if(info.game_type===61){
                this.ticket_count = (info.ticket_count===2) ? 0 : 1;
            }
            this.first_lossrate = this.anbaoProperty.first_lossrate.indexOf(info.first_lossrate);
            this.second_lossrate = this.anbaoProperty.second_lossrate.indexOf(info.second_lossrate);
            this.three_lossrate = this.anbaoProperty.three_lossrate.indexOf(info.three_lossrate);
            this.box_number = info.box_number;
            this.personId = this.anbaoInfo.account_id;
        }
    },
    methods: {
        cancelDetail(){
            this.$emit('cancelDetail');
        },
        modeSelect(index){
            if(this.enable){
                this.banker_mode = index;
            }
        },
        Lossrate_1(index){
            if(this.enable){
                this.first_lossrate = index;
            }
        },
        Lossrate_2(index){
            if(this.enable){
                this.second_lossrate = index;
            }
        },
        Lossrate_3(index){
            if(this.enable){
                this.three_lossrate = index;
            }
        },
        ticketSelect(index){
            if(this.enable){
                this.ticket_count = index;
            }
        },
        reduceLimitValue(){
            if(this.enable){
                if(Number(this.limitValue) === 0) return;
                this.limitValue = Number(this.limitValue) - 100;
            }
        },
        addLimitValue(){
            if(this.enable){
                if(Number(this.limitValue) === 2000) return;
                this.limitValue = Number(this.limitValue) + 100;
            }
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
            },
        saveBoxProperty(){
            var _this = this;
            var postData = {
                account_id: accountId,
                box_name: this.box_name,
                game_type: this.game_type,
                box_id:this.box_id,
                data: {
                    data_key:Date.parse(new Date())+randomString(5),
                    chip_type:this.chip_type,
                    ticket_count: Number((this.game_type === 61) ? (this.ticket_count===0 ? 2 : 4) : ''),
                    banker_mode: this.banker_mode + 1,
                    upper_limit: Number(this.limitValue),
                    first_lossrate:this.anbaoProperty.first_lossrate[this.first_lossrate],
                    second_lossrate:this.anbaoProperty.second_lossrate[this.second_lossrate],
                    three_lossrate:this.anbaoProperty.three_lossrate[this.three_lossrate],
                    game_type: this.game_type,
                    countDown: (function(){
                        return _this.countDown.map(function(value, index) {
                            return Number(value);
                        })
                    })()
                }
            };
            this.$http.post('/box/setBoxInfo', JSON.stringify(postData)).then(function(returnValue){
                var result = returnValue.data.result;
                if(result === 0){
                    console.log('修改成功');
                    this.$emit('checkData', postData.data);
                }
            })
        },
    },
}