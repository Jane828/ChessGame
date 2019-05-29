var bullP = {
    name:'bullP',
    props: ['gameTitle'],
    template: `
        <div class="bullP-wrap">
        {{gameTitle}}
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
                                    <div class="bullP-time-select" v-for="item in bullProperty.times">
                                        <span>{{item.text}}</span>
                                        <select>
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
                                    <div class="bullP-porker-item" v-for="(item, index) in bullProperty.porkers">
                                        <div class="bullP-porker-check" @click="selectPorker(index)">
                                        <img :src="baseUrl+'tick.png'" v-show="porkerObj[index]">
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
                                        <div class="bullP-num-check" @click="selectNum1(index)">
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
                                    <div class="bullP-up-item" v-for="item in bullProperty.ups">
                                        <div class="bullP-up-check">
                                        <img :src="baseUrl+'tick.png'">
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
                        <img :src="baseUrl+'box-save.png'" alt="保存" @click="saveBullBox">
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
                scoreSelect: true,
                scoreSelect1: true,
                baseUrl: baseUrl+'files/images/box/',
                gameTitle: this.gameTitle,
                customScore:100,
                customScore1:100,
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
        saveBullBox(){
            var _this = this;
            var postData = {
                account_id: accountId,
                game_type: returnGameType(this.gameTitle),
                box_name: (this.gameTitle === 'bull') ? '六人牛牛' : (this.gameTitle === 'nbull') ? '九人牛牛' : (this.gameTitle === 'tbull') ? '十人癞子牛' : (this.gameTitle==='twbull') ? '十二人牛牛' : (this.gameTitle==='thbull') ? '十三人牛牛' : '',
                data: {
                    data_key:Date.parse(new Date())+randomString(5),
                    score_type: (this.banker_mode===3) ? (this.scoreValue===3 ? customScore : this.bullProperty.tongScores[this.scoreValue]) : (this.scoreValue1===4 ? scoreValue1 : this.bullProperty.scores[this.scoreValue1]),
                }
            }
            console.log(postData)
        }
    }
}