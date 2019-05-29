var sgP = {
    name: 'sgP',
    props: ['gameTitle'],
    template: `
        <div class="sgP-wrap">
            <div class="sgP-area">
                <img :src="baseUrl+'close.png'" class="sgP-close" @click="cancelSgP">
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
                                        <div class="sgP-score-check" @click="selectScore(index)">
                                            <img :src="baseUrl+'tick.png'"  v-show="score_type===index">
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
                                        <select v-model="countDown[index]">
                                            <option v-for="value in item.select">{{value}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="sgP-rule">
                                <div class="sgP-property-title">规则：</div>
                                <div class="sgP-property-area">
                                    <div class="sgP-rule-item" v-for="(item,index) in sgProperty.rules">
                                        <div class="sgP-rule-check" @click="selectRule(index)">
                                            <img :src="baseUrl+'tick.png'" v-show="rules[index]">
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
                                    <div class="sgP-num-item" v-for="(item, index) in sgProperty.nums" v-if="gameTitle==='sg'">
                                        <div class="sgP-num-check" @click="selectNum(index)">
                                            <img :src="baseUrl+'tick.png'" v-show="ticket_type===index">
                                        </div>
                                        <div class="sgP-num-text">
                                            {{item}}
                                        </div>
                                    </div>
                                    <div class="sgP-num-item" v-for="(item, index1) in sgProperty.nineNums" v-if="gameTitle==='nsg'">
                                        <div class="sgP-num-check" @click="selectNum1(index1)">
                                            <img :src="baseUrl+'tick.png'" v-show="ticket_type1===index1">
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
                        <img :src="baseUrl+'box-save.png'" alt="保存" @click="saveSgBox">
                    </div>
                </div>
            </div>
        </div>
    `,
    data: function(){
        return {
            sgProperty: sgProperty,
            banker_mode: 0,
            score_type: 0,
            ticket_type: 0,
            gameTitle: this.gameTitle,
            ticket_type1: 0,
            baseUrl: baseUrl+'files/images/box/',
            countDown: [10,10,10,10],
            rules:[true, true],
        }
    },
    methods: {
        cancelSgP() {
            this.$emit('cancelSgP');
        },
        modeSelect(index){
            this.banker_mode = index;
        },
        selectScore(index){
            this.score_type = index;
        },
        selectRule(index){
            this.$set(this.rules, index, !this.rules[index]);
        },
        selectNum(index){
            this.ticket_type = index;
        },
        selectNum1(index){
            this.ticket_type1 = index;
        },
        saveSgBox(){
            var _this = this;
            var postData = {
                account_id: accountId,
                box_name: (this.gameTitle === 'sg') ? '六人三公' : '九人三公',
                game_type: returnGameType(this.gameTitle),
                data: {
                    data_key:Date.parse(new Date())+randomString(5),
                    banker_mode: this.banker_mode + 1,
                    score_type: this.sgProperty.scores[this.score_type],
                    countDown: this.countDown,
                    is_joker: this.rules[0] ? 1 : 0,
                    is_bj: this.rules[1] ? 1 : 0,
                    ticket_type: (this.gameTitle === 'sg') ? (this.ticket_type+1) : (this.ticket_type1+1),
                    game_type: returnGameType(this.gameTitle),
                }
            }
            console.log(baseUrl, imageUrl);
            this.$http.post('/box/addBox', JSON.stringify(postData)).then(function(returnValue){
                var result = returnValue.data.result;
                var data = returnValue.data;
                var message = returnValue.data.result_message;
                _this.$emit('sgReturn', message);
                console.log(returnValue);
            }).catch(function(){
                console.log('出错了');
            })
        }
    }
}