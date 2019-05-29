var bullP = {
    name:'bullP',
    props: ['gameTitle'],
    template: `
        <div class="bullP-wrap">
            <div class="bullP-area">
                <img :src="baseUrl+'close.png'" class="bullP-close" @click="cancelBullP">
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
                        <div class="bullP-mode-index" v-for="(item, index) in bullProperty.modes" @click="modeSelect(index)">
                            <img :src="baseUrl+'mode_selected.png'" v-if="banker_mode===index">
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
                                        <div class="bullP-score-check" @click="selectScore(index)">
                                            <img :src="baseUrl+'tick.png'"  v-show="scoreValue===index">
                                        </div>
                                        <div class="bullP-score-text">
                                            {{(index===3) ? item : (item+'分')}}
                                            <select v-show="index===3" :disabled="scoreSelect1" v-model="customScore">
                                                <option v-for="num in bullProperty.scoreList()">{{num}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="bullP-score-item" v-for="(item, index1) in bullProperty.scores" v-show="banker_mode!==3">
                                        <div class="bullP-score-check" @click="selectScore1(index1)">
                                            <img :src="baseUrl+'tick.png'"  v-show="scoreValue1===index1">
                                        </div>
                                        <div class="bullP-score-text">
                                            {{(index1===4) ? item : (item+'分')}}
                                            <select v-show="index1===4" :disabled="scoreSelect" v-model="customScore1">
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
                                        <select v-model="defaultTimes[index]">
                                            <option v-for="value in item.select">{{value}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="bullP-rule">
                                <div class="bullP-property-title">规则：</div>
                                <div class="bullP-property-area">
                                    <div class="bullP-rule-item" v-for="(item,index) in bullProperty.rules">
                                        <div class="bullP-rule-check" @click="selectRule(index)">
                                            <img :src="baseUrl+'tick.png'" v-show="ruleValue===index">
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
                                    <div class="bullP-porker-item" v-for="(item, index) in bullProperty.porkers" v-if="gameTitle!=='bull' && gameTitle!=='nbull'">
                                        <div class="bullP-porker-check" @click="selectPorker(index)">
                                        <img :src="baseUrl+'tick.png'" v-show="porkerObj[index]">
                                        </div>
                                        <div class="bullP-porker-text">
                                            {{item}}
                                        </div>
                                    </div>
                                    <div class="bullP-porker-item" v-for="(item, index) in bullProperty.lporkers" v-if="gameTitle==='bull' || gameTitle==='nbull'">
                                        <div class="bullP-porker-check" @click="selectPorker1(index)">
                                        <img :src="baseUrl+'tick.png'" v-show="porkerObj1[index]">
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
                                    <div class="bullP-num-item" v-for="(item, index) in bullProperty.nums" v-if="gameTitle!=='bull'">
                                        <div class="bullP-num-check" @click="selectNum(index)">
                                            <img :src="baseUrl+'tick.png'" v-show="numValue===index">
                                        </div>
                                        <div class="bullP-num-text">
                                            {{item}}
                                        </div>
                                    </div>
                                    <div class="bullP-num-item" v-for="(item, index1) in bullProperty.bullNums" v-if="gameTitle==='bull'">
                                        <div class="bullP-num-check" @click="selectNum1(index1)">
                                            <img :src="baseUrl+'tick.png'" v-show="numValue1===index1">
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
                                        <div class="bullP-multiple-check" @click="selectMultiple(index)">
                                        <img :src="baseUrl+'tick.png'" v-show="multipleValue===index">
                                        </div>
                                        <div class="bullP-multiple-text">
                                            {{item}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bullP-up" v-show="banker_mode===4">
                                <div class="bullP-property-title">上庄：</div>
                                <div class="bullP-property-area">
                                    <div class="bullP-up-item" v-for="(item, index) in bullProperty.ups">
                                        <div class="bullP-up-check" @click="selectUp(index)">
                                        <img :src="baseUrl+'tick.png'" v-show="upZhu===index">
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
                        <img :src="baseUrl+'box-save.png'" alt="保存" @click="saveBullBox" v-if="btnDel">
                        <img :src="baseUrl+'box-save.png'" alt="保存" v-if="!btnDel">
                    </div>
                </div>
            </div>
        </div>
    `,
    data: function() {
        return {
                bullProperty: bullProperty,
                banker_mode: 1,
                ruleValue: 0,
                numValue: 0,
                numValue1: 0,
                multipleValue: 0,
                scoreValue: 0,
                scoreValue1: 0,
                porkerObj: [true, true, true, true, true, true, true, true],
                porkerObj1: [true, true, true, true, true, true, true, true, true],
                scoreSelect: true,
                scoreSelect1: true,
                baseUrl: baseUrl+'files/images/box/',
                gameTitle: this.gameTitle,
                customScore:100,
                customScore1:100,
                defaultTimes: [6,6,7,8],
                ruleShow: true,
                upZhu: 0,
                btnDel: true
        }
    },
    methods: {
        modeSelect(index){
            this.banker_mode = index;
        },
        selectRule(index){
            this.ruleValue = index;
        },
        selectNum(index){
            this.numValue = index;
        },
        selectNum1(index){
            this.numValue1 = index;
        },
        selectMultiple(index) {
            this.multipleValue = index;
        },
        selectPorker(index){
            this.$set(this.porkerObj, index, !this.porkerObj[index]);        
            console.log(this.porkerObj)
        },
        selectPorker1(index){
            this.$set(this.porkerObj1, index, !this.porkerObj1[index]);        
            console.log(this.porkerObj1);
        },
        selectScore1(index){
            this.scoreSelect = (index === 4) ? false : true;
            this.scoreValue1 = index;
        },
        selectScore(index){
            this.scoreSelect1 = (index === 3) ? false : true;
            this.scoreValue = index;
        },
        cancelBullP() {
            this.banker_mode =  1;
            this.$emit('cancelBullP');
        },
        selectUp(index){
            this.upZhu = index;
        },
        // scoreType参数是牛牛底分
        selectScoreType1(scoreType){
            var arr = [1,6,2,3,7];
            return arr[scoreType];
        },
        selectScoreType(scoreType){
            var arr = [3, 4, 5, 7];
            return arr[scoreType];
        },
        saveBullBox(){
            var _this = this;
            this.btnDel = false;
            var postData = {
                account_id: accountId,
                game_type: returnGameType(this.gameTitle),
                box_name: (this.gameTitle === 'bull') ? '六人牛牛' : (this.gameTitle === 'nbull') ? '九人牛牛' : (this.gameTitle === 'tbull') ? '十人癞子牛' : (this.gameTitle==='twbull') ? '十二人牛牛' : (this.gameTitle==='thbull') ? '十三人牛牛' : '',
                data: {
                    data_key:Date.parse(new Date())+randomString(5),
                    banker_mode: this.banker_mode+1,
                    score_type: (_this.banker_mode===3) ? this.selectScoreType(this.scoreValue) : this.selectScoreType1(this.scoreValue1),
                    game_type: returnGameType(this.gameTitle),
                    countDown: (function(){
                        return _this.defaultTimes.map(function(value, index) {
                            return Number(value);
                        })
                    })(),
                    rule_type: this.ruleValue+1,
                    ticket_type: (this.gameTitle === 'bull') ? (this.numValue1+1) : (this.numValue+1),
                    times_type: this.multipleValue+1,
                    is_cardfour: ((this.gameTitle === 'bull') || (this.gameTitle === 'nbull')) ? (this.porkerObj1[1] ? 1 : 0) : (this.porkerObj[0] ? 1 : 0),
                    is_cardfive: ((this.gameTitle === 'bull') || (this.gameTitle === 'nbull')) ? (this.porkerObj1[2] ? 1 : 0) : (this.porkerObj[1] ? 1 : 0),
                    is_straight: ((this.gameTitle === 'bull') || (this.gameTitle === 'nbull')) ? (this.porkerObj1[3] ? 1 : 0) : (this.porkerObj[2] ? 1 : 0),
                    is_flush: ((this.gameTitle === 'bull') || (this.gameTitle === 'nbull')) ? (this.porkerObj1[4] ? 1 : 0) : (this.porkerObj[3] ? 1 : 0),
                    is_hulu: ((this.gameTitle === 'bull') || (this.gameTitle === 'nbull')) ? (this.porkerObj1[5] ? 1 : 0) : (this.porkerObj[4] ? 1 : 0),
                    is_cardbomb: ((this.gameTitle === 'bull') || (this.gameTitle === 'nbull')) ? (this.porkerObj1[6] ? 1 : 0) : (this.porkerObj[5] ? 1 : 0),
                    is_straightflush: ((this.gameTitle === 'bull') || (this.gameTitle === 'nbull')) ? (this.porkerObj1[7] ? 1 : 0) : (this.porkerObj[6] ? 1 : 0),
                    is_cardtiny: ((this.gameTitle === 'bull') || (this.gameTitle === 'nbull')) ? (this.porkerObj1[8] ? 1 : 0) : (this.porkerObj[7] ? 1 : 0),
                    banker_score_type: this.upZhu + 1,
                    //自定义底分，下拉框
                    score_value: (this.banker_mode===3) ? this.customScore : this.customScore1,

                }
            }
            if(this.gameTitle==='bull' || this.gameTitle==='nbull'){
                postData.data.has_ghost = (this.porkerObj1[0] ? 1 : 0);
            }
            console.log(postData)
            this.$http.post('/box/addBox', JSON.stringify(postData))
                      .then(function(returnValue) {
                        var result = returnValue.data.result;
                        if(result===0){
                            var message = returnValue.data.result_message;
                            _this.$emit('bullReturn', message);
                        }else if(result===-1){
                            var message = returnValue.data.result_message;
                            _this.$emit('bullReturn', message);
                        }
                      })
                      .catch(function(error) {
                          console.log('出错了');
                      });
        }
    },
    beforeMount:function(){
        this.btnDel = true;
        console.log(this.btnDel)
    }
}