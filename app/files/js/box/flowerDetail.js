var flowerDetail = {
    name: 'flowerDetail',
    props:['flowerInfo', 'enable', 'isRoomOrBox', 'roomNumber'],
    template: `
    <div class="flowerP-wrap" lang="en">
        <div  v-if="alert" style="width:60vw; height:20vh;text-align:center;background: rgba(0,0,0,0.5);position:absolute; z-index:15; top:30vh; left:50%; transform:translateX(-50%); ">
            <p style="text-align:center;color:white;margin-top:6vh;">请选择4组筹码(必须为4组)</p>
            <button @click="cancelAlert" style="outline: none; width: 20vw;height:5vh; border:none; border-radius:10px; color:white; background:chocolate;margin-top:3vh;">确定</button>
        </div>  
        <div class="flowerP-area">
        <img :src="baseUrl + 'close.png'" class="flowerP-close" @click="cancelDetail" >
            <div class="flowerP-title">
                <img :src="baseUrl + 'storetitle.png'" alt="房间设置">
                <span>房间设置</span>
            </div>
            <div class="flowerP-show">
                <div class="flowerP-tips">
                    <div class="flowerP-tips-before"></div>
                    <div class="flowerP-tips-text">创建房间，游戏未进行不消耗房卡呦</div>
                    <div class="flowerP-tips-after"></div>
                </div>
                <div class="flowerP-control">
                    <img :src="baseUrl+'settingback.png'">
                    <div class="flowerP-property">
                        <div class="flowerP-score">
                            <div class="flowerP-score-title">
                            底分：
                            </div>
                            <div class="flowerP-score-property">
                                <div class="flowerP-score-item" v-for="(item, index) in flowerProperty.scores">
                                    <div class="flowerP-score-check" @click="scoreSelect(index)">
                                        <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-if="default_score===index">
                                    </div>
                                    <div class="flowerP-score-text">
                                        {{item===0 ? '' : item}}
                                        <select v-show="index===7" :disabled="scoreList||!enable" v-model="sevenScore">
                                            <option v-for="num in flowerProperty.scoreList()">{{num}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flowerP-chip">
                            <div class="flowerP-score-title">
                            筹码：
                            </div>
                            <div class="flowerP-score-property">
                                <div class="flowerP-score-item" v-for="(item, index) in flowerProperty.chips">
                                    <div class="flowerP-score-check" @click="chipSelect(index)">
                                        <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-if="chips.indexOf(index)!==-1">
                                    </div>
                                    <div class="flowerP-score-text">
                                        {{item}}
                                    </div>
                                </div>
                            </div>
                            <div class="flowerP-chip-tips">请选择四组筹码</div>
                        </div>
                        <div class="flowerP-time">
                            <div class="flowerP-score-title">
                            时间：
                            </div>
                            <div class="flowerP-score-property">
                                <div class="flowerP-time-select">
                                    <span>准备</span>
                                    <select v-model="countDown[0]" :disabled="!enable">
                                        <option v-for="item in flowerProperty.date.ready">{{item}}</option>
                                    </select>
                                </div>
                                <div class="flowerP-time-select">
                                    <span>下注</span>
                                    <select v-model="countDown[1]" :disabled="!enable">
                                        <option v-for="item in flowerProperty.date.toChips">{{item}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="flowerP-seen">
                            <div class="flowerP-score-title">
                            看牌：
                            </div>
                            <div class="flowerP-score-property">
                                <div class="flowerP-seen-tips">低于积分池将不能看牌</div>
                                <div class="flowerP-seen-range">
                                    <img :src="enable ? baseUrl+'reduce.png' : baseUrl+'disabledreduce.png'" class="seen-select-reduce" @click="reduceSeenProgress">
                                    <span class="seen-range-value">{{(Number(seenProgress) === 0) ? '无上限' : seenProgress}}</span>
                                    <input :disabled="!enable" type="range"  min="0"  step="100" :max="flowerProperty.seenLimit" class="seen-range" v-model="seenProgress" :style="{backgroundSize:seenProgress/20 + '% 100%',}">
                                    <img :src="enable ? baseUrl+'add.png' : baseUrl + 'disabledadd.png'" class="seen-select-add" @click="addSeenProgress">
                                </div>
                            </div>
                        </div>
                        <div class="flowerP-compare">
                            <div class="flowerP-score-title">
                            比牌：
                            </div>
                            <div class="flowerP-score-property">
                                <div class="flowerP-compare-item">
                                    <div class="flowerP-compare-check" @click="raceSelect">
                                        <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-if="!raceCard">
                                    </div>
                                    <div class="flowerP-compare-text">
                                        {{flowerProperty.firstCompare}}
                                    </div>
                                </div>
                                <div class="flowerP-compare-tips">
                                    低于积分池将不能比牌
                                </div>
                                <div class="flowerP-seen-range">
                                    <img :src="enable ? baseUrl+'reduce.png' : baseUrl+'disabledreduce.png' " class="seen-select-reduce" @click="reduceCompareProgress">
                                    <span class="seen-range-value">{{(Number(compareProgress) === 0) ? '无上限' : compareProgress}}</span>
                                    <input :disabled="!enable" type="range" min="0" v-model="compareProgress" :max="flowerProperty.compareLimit" step="100" class="seen-range" :style="{backgroundSize:compareProgress/20 + '% 100%',}">
                                    <img :src="enable ? baseUrl+'add.png' : baseUrl+'disabledadd.png'" class="seen-select-add" @click="addCompareProgress">
                                </div>
                            </div>
                        </div>
                        <div class="flowerP-ju">
                            <div class="flowerP-score-title">
                            局数：
                            </div>
                            <div class="flowerP-score-property">
                               <div class="flowerP-ju-item"  v-for="(item, index) in flowerProperty.tenBoardNum" v-if="game_type===110">
                                    <div class="flowerP-ju-check" @click="ticketSelect(index)">
                                        <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-if="ticket_count===index">
                                    </div>
                                    <div class="flowerP-ju-text">
                                        {{item}}
                                    </div>
                               </div>
                               <div class="flowerP-ju-item"  v-for="(item, index1) in flowerProperty.boardNum" v-if="game_type!==110">
                                    <div class="flowerP-ju-check" @click="ticketSelect1(index1)">
                                        <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-if="ticket_count1===index1">
                                    </div>
                                    <div class="flowerP-ju-text">
                                        {{item}}
                                    </div>
                               </div>
                            </div>
                        </div>
                        <div class="flowerP-up">
                            <div class="flowerP-up-title">
                            上限：
                            </div>
                            <div class="flowerP-score-property">
                                <div class="flowerP-seen-range">
                                    <img :src="enable ? baseUrl+'reduce.png' : baseUrl+'disabledreduce.png'" class="seen-select-reduce" @click="reduceUpperLimit">
                                    <span class="seen-range-value">{{(Number(upper_limit) === 0) ? '无上限' : upper_limit}}</span>
                                    <input :disabled="!enable" type="range" min="0" :max="flowerProperty.upLimit" step="100" class="seen-range" v-model="upper_limit" :style="{backgroundSize:upper_limit/20 + '% 100%',}">
                                    <img :src="enable ? baseUrl+'add.png' : baseUrl+'disabledadd.png'" class="seen-select-add" @click="addUpperLimit">
                                </div>
                            </div>
                        </div>
                        <div class="flowerP-xi">
                            <div class="flowerP-score-title">
                            喜牌：
                            </div>
                            <div class="flowerP-score-property">
                                <div class="flowerP-xi-tips">
                                    豹子，同花顺为喜牌，获得玩家奖励
                                </div>
                                <div class="flowerP-xi-item" v-for="(item, index) in flowerProperty.happyCards">
                                    <div class="flowerP-xi-check" @click="xiSelect(index)">
                                        <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-if="extraRewards===index">
                                    </div>
                                    <div class="flowerP-xi-text">
                                        {{item}}
                                    </div>
                               </div>
                            </div>
                        </div>
                        <div class="flowerP-special" v-if="game_type!==111">
                            <div class="flowerP-score-title">
                                特殊：
                            </div>
                            <div class="flowerP-score-property">
                                <div class="flowerP-special-item">
                                    <div class="flowerP-special-check" @click="eatSelect">
                                        <img :src="enable ? baseUrl+'tick.png' : baseUrl+'disabledTick.png'" v-if="allow235GTPanther">
                                    </div>
                                <div class="flowerP-special-text">
                                    {{flowerProperty.special}}
                                </div>
                               </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flowerP-save">
                    <img :src="baseUrl+'joingame.png'" alt="保存" @click="toJoinGame" v-if="!enable">
                    <img :src="baseUrl+'box-save.png'" alt="保存" @click="saveBoxProperty" v-if="enable">
                </div>
            </div>
        </div>
    </div>
    `,
    data: function () {
        return {
            flowerProperty: flowerProperty,
            baseUrl: baseUrl + 'files/images/box/',
            default_score: 0,
            chips: [],
            countDown: [],
            seenProgress: 0,
            compareProgress: 0,
            raceCard: false,
            game_type: 0,
            scoreList: true,
            ticket_count: 0,
            ticket_count1: 0,
            upper_limit: 0,
            extraRewards: 0,
            allow235GTPanther: 0,
            personId: '',
            box_number: 0,
            box_name: '',
            box_id: '',
            sevenScore: 100,
            alert: false
        }
    },
    methods:{
        cancelAlert(){
            this.alert = false;
        },
        reduceSeenProgress(){
            if(this.enable){
                if(Number(this.seenProgress)===0) return;
                this.seenProgress = Number(this.seenProgress) - 100;
            }
        },
        addSeenProgress(){
            if(this.enable){
                if(Number(this.seenProgress)===2000) return;
                this.seenProgress = Number(this.seenProgress) + 100;
            }
        },
        reduceCompareProgress(){
            if(this.enable){
                if(Number(this.compareProgress)===0) return;
                this.compareProgress = Number(this.compareProgress) - 100;
            }
        },
        addCompareProgress(){
            if(this.enable){
                if(Number(this.compareProgress)===2000) return;
                this.compareProgress = Number(this.compareProgress) + 100;
            }
        },
        reduceUpperLimit(){
            if(this.enable){
                if(Number(this.upper_limit)===0) return;
                this.upper_limit = Number(this.upper_limit) - 100;
            }
        },
        addUpperLimit(){
            if(this.enable){
                if(Number(this.upper_limit)===2000) return;
                this.upper_limit = Number(this.upper_limit) + 100;
            }
        },
        cancelDetail(){
            this.$emit('cancelDetail');
        },
        // 底分选择
        scoreSelect(index){
            if(this.enable){
                this.scoreList = (index===7) ? false : true;
                this.default_score  = index;
            }
        },
        // 筹码选择
        chipSelect(index){
            if(this.enable){
            var ins = this.chips.indexOf(index);
            if(ins===-1){
                if(this.chips.length<4){
                    this.chips.push(index);
                    this.chips.sort(function(a, b){return a - b});
                }
            }else {
                this.chips.splice(ins, 1);
            }
        }
        },
        ticketSelect(index){
            if(this.enable){
            this.ticket_count = index;
            }
        },
        ticketSelect1(index){
            if(this.enable){
            this.ticket_count1 = index;
            }
        },
        raceSelect(){
            if(this.enable){
            this.raceCard = !this.raceCard;
            }
        },
        xiSelect(index){
            if(this.enable){
            this.extraRewards = index;
            }
        },
        eatSelect(){
            if(this.enable){
            if(this.allow235GTPanther===1){
                this.allow235GTPanther = 0;
            }else if(this.allow235GTPanther === 0){
                this.allow235GTPanther = 1;
            }
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
            if(this.chips.length !==4) {
                this.alert = true;
                return;
            }
            var postData = {
                account_id: accountId,
                box_name: this.box_name,
                game_type: this.game_type,
                box_id: this.box_id,
                data: {
                    data_key:Date.parse(new Date())+randomString(5),
                    default_score: Number((this.default_score !== 7) ? this.flowerProperty.scores[this.default_score] : this.sevenScore),
                    chip_type:(function(){
                        return _this.chips.map(function(value, index) {
                            return Number(_this.flowerProperty.chips[value].substr(0, _this.flowerProperty.chips[value].indexOf('/')));
                        })
                    })(),
                    countDown: (function(){
                        return _this.countDown.map(function(value, index) {
                            return Number(value);
                        })
                    })(),
                    seenProgress: Number(this.seenProgress),
                    compareProgress: Number(this.compareProgress),
                    raceCard: this.raceCard,
                    upper_limit: Number(this.upper_limit),
                    ticket_count: Number((this.game_type === 110) ? (this.ticket_count===0 ? 2 : 4) : (this.ticket_count1===0 ? 1 :2)),
                    extraRewards: this.flowerProperty.happyCards[this.extraRewards],
                    game_type: this.game_type,
                    disable_pk_men: 0,
                    allow235GTPanther:(this.allow235GTPanther===1) ? 1 : 0
                }
            };
            this.$http.post('/box/setBoxInfo', JSON.stringify(postData)).then(function(returnValue){
                var result = returnValue.data.result;
                if(result === 0){
                    console.log('修改成功');
                    this.$emit('checkData', postData.data);
                }
            })
        }
    },
    beforeMount: function(){
	console.log("djoiad");
	console.log(this.flowerInfo);
	console.log(this.enable);
        if(this.enable){
            var _this = this;
            var info = JSON.parse(this.flowerInfo.config);
            this.default_score = (this.flowerProperty.scores.indexOf(info.default_score)===-1 ? 7 : this.flowerProperty.scores.indexOf(info.default_score));
            if(this.default_score===7){
                this.sevenScore = info.default_score;
            }
            info.chip_type.map(function(value){
                _this.flowerProperty.chips.map(function(value1, index){
                    var front = Number(value1.substr(0, value1.indexOf('/')));
                    if(value===front){
                        _this.chips.push(index);
                    }
                })
            });
            this.countDown = info.countDown;
            this.seenProgress = info.seenProgress;
            this.compareProgress = info.compareProgress;
            this.raceCard = info.raceCard;
            this.game_type = info.game_type;
            this.upper_limit = info.upper_limit;
            if(info.game_type===110){
                this.ticket_count = (info.ticket_count===2) ? 0 : 1;
            }else {
                this.ticket_count1 = (info.ticket_count===1) ? 0 : 1;
            }
            this.extraRewards = this.flowerProperty.happyCards.indexOf(info.extraRewards);
            this.allow235GTPanther = info.allow235GTPanther;
            this.box_number = info.box_number;
            this.personId = this.flowerInfo.account_id;
            this.box_name = this.flowerInfo.box_name;
            this.box_id = this.flowerInfo.box_id;
        }else{
            var _this = this;
            var info = JSON.parse(this.flowerInfo.data.config);
            this.default_score = (this.flowerProperty.scores.indexOf(info.default_score)===-1 ? 7 : this.flowerProperty.scores.indexOf(info.default_score));
            if(this.default_score===7){
                this.sevenScore = info.default_score;
            }
            info.chip_type.map(function(value){
                _this.flowerProperty.chips.map(function(value1, index){
                    var front = Number(value1.substr(0, value1.indexOf('/')));
                    if(value===front){
                        _this.chips.push(index);
                    }
                })
            });
            this.countDown = info.countDown;
            this.seenProgress = info.seenProgress;
            this.compareProgress = info.compareProgress;
            this.raceCard = info.raceCard;
            this.game_type = info.game_type;
            this.upper_limit = info.upper_limit;
            if(info.game_type===110){
                this.ticket_count = (info.ticket_count===2) ? 0 : 1;
            }else {
                this.ticket_count1 = (info.ticket_count===1) ? 0 : 1;
            }
            this.extraRewards = this.flowerProperty.happyCards.indexOf(info.extraRewards);
            this.allow235GTPanther = info.allow235GTPanther;
            this.box_number = info.box_number;
            this.personId = this.flowerInfo.account_id;
        }
    }
}
