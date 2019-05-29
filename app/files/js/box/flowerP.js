var flowerP = {
    name: 'flowerP',
    props: ['gameTitle'],
    template: `
    <div class="flowerP-wrap">
        <div  v-if="alert" style="width:60vw; height:20vh;text-align:center;background: rgba(0,0,0,0.5);position:absolute; z-index:15; top:30vh; left:50%; transform:translateX(-50%); ">
            <p style="text-align:center;color:white;margin-top:6vh;">请选择4组筹码(必须为4组)</p>
            <button @click="cancelAlert" style="outline: none; width: 20vw;height:5vh; border:none; border-radius:10px; color:white; background:chocolate;margin-top:3vh;">确定</button>
        </div>
        <div class="flowerP-area">
        <img :src="baseUrl + 'close.png'" class="flowerP-close" @click="cancelFlowerP">
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
                                        <img :src="baseUrl+'tick.png'" v-show="index===default_score">
                                    </div>
                                    <div class="flowerP-score-text">
                                        {{item===0 ? '' : item}}
                                        <select v-show="index===7" :disabled="scoreEnable" v-model="scoreSelectValue">
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
                                        <img :src="baseUrl+'tick.png'" v-show="chips.indexOf(index)!==-1">
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
                                    <select v-model="countDown[0]">
                                        <option v-for="item in flowerProperty.date.ready">{{item}}</option>
                                    </select>
                                </div>
                                <div class="flowerP-time-select">
                                    <span>下注</span>
                                    <select v-model="countDown[1]">
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
                                    <img :src="baseUrl+'reduce.png'" class="seen-select-reduce" @click="reduceSeenValue">
                                    <span class="seen-range-value">{{(Number(seenValue) === 0) ? '无上限' : seenValue}}</span>
                                    <input type="range" v-model="seenValue" min="0" :max="flowerProperty.seenLimit" step="100" class="seen-range" :style="{backgroundSize:seenValue/20 + '% 100%',}">
                                    <img :src="baseUrl+'add.png'" class="seen-select-add" @click="addSeenValue">
                                </div>
                            </div>
                        </div>
                        <div class="flowerP-compare">
                            <div class="flowerP-score-title">
                            比牌：
                            </div>
                            <div class="flowerP-score-property">
                                <div class="flowerP-compare-item">
                                    <div class="flowerP-compare-check" @click="compareSelect">
                                        <img :src="baseUrl+'tick.png'" v-show="!raceCard">
                                    </div>
                                    <div class="flowerP-compare-text">
                                        {{flowerProperty.firstCompare}}
                                    </div>
                                </div>
                                <div class="flowerP-compare-tips">
                                    低于积分池将不能比牌
                                </div>
                                <div class="flowerP-seen-range">
                                    <img :src="baseUrl+'reduce.png'" class="seen-select-reduce" @click="reduceCompareValue">
                                    <span class="seen-range-value">{{(Number(compareValue) === 0) ? '无上限' : compareValue}}</span>
                                    <input type="range" min="0" v-model="compareValue" :max="flowerProperty.compareLimit" step="100" class="seen-range" :style="{backgroundSize: compareValue/20 + '% 100%'}">
                                    <img :src="baseUrl+'add.png'" class="seen-select-add" @click="addCompareValue">
                                </div>
                            </div>
                        </div>
                        <div class="flowerP-ju">
                            <div class="flowerP-score-title">
                            局数：
                            </div>
                            <div class="flowerP-score-property">
                               <div class="flowerP-ju-item" v-if="gameTitle==='tflower'" v-for="(item, index) in flowerProperty.tenBoardNum">
                                    <div class="flowerP-ju-check" @click="ticketSelect(index)">
                                        <img :src="baseUrl+'tick.png'" v-show="ticket_count===index">
                                    </div>
                                    <div class="flowerP-ju-text">
                                        {{item}}
                                    </div>
                               </div>
                               <div class="flowerP-ju-item" v-if="gameTitle!=='tflower'" v-for="(item, index1) in flowerProperty.boardNum">
                                    <div class="flowerP-ju-check"  @click="ticketSelect1(index1)">
                                        <img :src="baseUrl+'tick.png'" v-show="ticket_count1===index1">
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
                                    <img :src="baseUrl+'reduce.png'" class="seen-select-reduce" @click="reduceLimitValue">
                                    <span class="seen-range-value">{{(Number(limitValue) === 0) ? '无上限' : limitValue}}</span>
                                    <input type="range" min="0" v-model="limitValue" :max="flowerProperty.upLimit" step="100" class="seen-range" :style="{backgroundSize: limitValue/20 + '% 100%',}">
                                    <img :src="baseUrl+'add.png'" class="seen-select-add" @click="addLimitValue">
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
                                        <img :src="baseUrl+'tick.png'" v-show="extraRewards===index">
                                    </div>
                                    <div class="flowerP-xi-text">
                                        {{item}}
                                    </div>
                               </div>
                            </div>
                        </div>
                        <div class="flowerP-special" v-show="gameTitle!=='bflower'">
                            <div class="flowerP-score-title">
                                特殊：
                            </div>
                            <div class="flowerP-score-property">
                                <div class="flowerP-special-item">
                                    <div class="flowerP-special-check" @click="specialSelect">
                                        <img :src="baseUrl+'tick.png'" v-show="allow235GTPanther">
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
                    <img :src="baseUrl+'box-save.png'" alt="保存"  @click="saveFlowerBox" v-if="btnDel">
                    <img :src="baseUrl+'box-save.png'" alt="保存"  v-if="!btnDel">
                </div>
            </div>
        </div>
    </div>
    `,
    data: function() {
        return {
            baseUrl: baseUrl+'files/images/box/',
            flowerProperty: flowerProperty,
            gameTitle: this.gameTitle,
            default_score: 0,
            scoreEnable: true,
            scoreSelectValue: 200,
            chips:[0,1,3,4],
            countDown: [10, 10],
            seenValue:1000,
            limitValue:1000,
            compareValue: 1000,
            raceCard:false,
            ticket_count:0,
            ticket_count1: 0,
            extraRewards: 0,
            allow235GTPanther: false,
            alert: false,
            btnDel: true
        }
    },
    methods: {
        cancelAlert(){
            this.alert = false;
        },
        reduceSeenValue(){
            if(Number(this.seenValue)===0) return;
            this.seenValue = Number(this.seenValue) - 100;
        },
        addSeenValue(){
            if(Number(this.seenValue)===2000) return;
            this.seenValue = Number(this.seenValue) + 100;
        },
        reduceCompareValue(){
            if(Number(this.compareValue)===0) return;
            this.compareValue = Number(this.compareValue) - 100;
        },
        addCompareValue(){
            if(Number(this.compareValue)===2000) return;
            this.compareValue = Number(this.compareValue) + 100;
        },
        reduceLimitValue(){
            if(Number(this.limitValue) === 0) return;
            this.limitValue = Number(this.limitValue) - 100;
        },
        addLimitValue(){
            if(Number(this.limitValue) === 2000) return;
            this.limitValue = Number(this.limitValue) + 100;
        },
        cancelFlowerP(){
            this.$emit('cancelFlowerP');
        },
        scoreSelect(index){
            this.scoreEnable = (index!==7) ? true : false;
            this.default_score = index;
        },
        chipSelect(index){
            var ins = this.chips.indexOf(index);
            console.log(ins);
            if(ins===-1){
                if(this.chips.length !== 4){
                    this.chips.push(index);
                    this.chips.sort(function(a, b){ return a - b; });
                }
            }else{
                this.chips.splice(ins,1);
            }
        },
        compareSelect(){
            this.raceCard = !this.raceCard;
        },
        ticketSelect(index){
            this.ticket_count = index;
        },
        ticketSelect1(index){
            this.ticket_count1 = index;
        },
        xiSelect(index){
            this.extraRewards = index;
        },
        specialSelect(){
            this.allow235GTPanther = !this.allow235GTPanther;
        },
        saveFlowerBox(){
            var _this = this;
            if(this.chips.length!==4){
                this.alert = true;
                return;
            }
            this.btnDel = false;
            var postData = {
                account_id: accountId,
                box_name: (this.gameTitle === 'flower') ? '飘三叶' : (this.gameTitle === 'tflower') ? '十人飘三叶' : '大牌飘三叶',
                game_type: returnGameType(this.gameTitle),
                data: {
                    data_key:Date.parse(new Date())+randomString(5),
                    default_score: Number((this.default_score !== 7) ? this.flowerProperty.scores[this.default_score] : this.scoreSelectValue),
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
                    seenProgress: Number(this.seenValue),
                    compareProgress: Number(this.compareValue),
                    raceCard: this.raceCard,
                    upper_limit: Number(this.limitValue),
                    ticket_count: Number((this.gameTitle === 'tflower') ? (this.ticket_count===0 ? 2 : 4) : (this.ticket_count1===0 ? 1 :2)),
                    extraRewards: this.flowerProperty.happyCards[this.extraRewards],
                    game_type: returnGameType(this.gameTitle),
                    disable_pk_men: 0,
                    allow235GTPanther:(this.allow235GTPanther) ? 1 : 0
                }
            };
            console.log(postData);
            this.$http.post('/box/addBox', JSON.stringify(postData)).then(function(returnValue){
                var result = returnValue.data.result;
                console.log('----flowerSave:'+ JSON.stringify(returnValue))
                // 如果新建包厢成功
                if(result===0){
                    var message = returnValue.data.result_message;
                    _this.$emit('flowerReturn', message);
                }else if(result===-1){
                    var message = returnValue.data.result_message;
                    _this.$emit('flowerReturn', message);
                }
            })
        },
    },
    beforeMount: function(){
        this.btnDel = true;
    }
}