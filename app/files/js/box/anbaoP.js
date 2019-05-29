var anbaoP = {
    name: 'anbaoP',
    props: ['gameTitle'],
    template:  `
               <div class="anbaoP-wrap">
                   <div class="anbaoP-area">
                       <img :src="baseUrl+'close.png'" class="anbaoP-close" @click="cancelAnbaoP">
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
                                               <img :src="baseUrl+'tick.png'"/>
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
                                               <img :src="baseUrl+'reduce.png'" class="seen-select-reduce" @click="reduceLimitValue">
                                               <span class="seen-range-value">{{(Number(limitValue) === 0) ? '无上限' : limitValue}}</span>
                                               <input type="range" min="0" v-model="limitValue" :max="anbaoProperty.upLimit" step="100" class="seen-range" :style="{backgroundSize: limitValue/20 + '% 100%',}">
                                               <img :src="baseUrl+'add.png'" class="seen-select-add" @click="addLimitValue">
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
                                                   <img :src="baseUrl+'tick.png'" v-show="first_lossrate===index">
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
                                                   <img :src="baseUrl+'tick.png'" v-show="second_lossrate===index">
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
                                                   <img :src="baseUrl+'tick.png'" v-show="three_lossrate===index">
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
                                               <select v-model="countDown[index]">
                                                   <option v-for="value in item.select">{{value}}</option>
                                               </select>
                                           </div>
                                       </div>
                                   </div>
                                   <div class="anbaoP-num">
                                       <div class="anbaoP-property-title">局数：</div>
                                       <div class="anbaoP-num-item" v-for="(item, index) in anbaoProperty.boardNum">
                                           <div class="anbaoP-odds-check" @click="ticketSelect(index)">
                                               <img :src="baseUrl+'tick.png'" v-show="ticket_count===index">
                                           </div>
                                           <div class="anbaoP-odds-text">
                                               {{item}}
                                           </div>
                                       </div>
                                   </div>
                                   </div>
                               </div>
                           <div class="anbaoP-save" >
                               <img :src="baseUrl+'box-save.png'" alt="保存" @click="saveAnbaoBox" v-if="btnDel"> 
                               <img :src="baseUrl+'box-save.png'" alt="保存"  v-if="!btnDel">
                           </div>
                       </div>
                   </div>
               </div>
               `,
    data: function(){
        return {
            anbaoProperty: anbaoProperty,
            gameTitle: this.gameTitle,
            banker_mode: 0,
            chip_type:[10,20,30,50,100],
            limitValue:600,
            extraRewards: 0,
            ticket_count:0,
            first_lossrate: 0,
            second_lossrate:0,
            three_lossrate: 0,
            countDown: [30,30,10,30,10],
            baseUrl: baseUrl+'files/images/box/',
            btnDel: true
        }
    },
    methods: {
        cancelAnbaoP() {
            this.$emit('cancelAnbaoP');
        },
        modeSelect(index){
            this.banker_mode = index;
        },
        Lossrate_1(index){
            this.first_lossrate = index;
        },
        Lossrate_2(index){
            this.second_lossrate = index;
        },
        Lossrate_3(index){
            this.three_lossrate = index;
        },
        ticketSelect(index){
            this.ticket_count = index;
        },
        reduceLimitValue(){
            if(Number(this.limitValue) === 0) return;
            this.limitValue = Number(this.limitValue) - 100;
        },
        addLimitValue(){
            if(Number(this.limitValue) === 2000) return;
            this.limitValue = Number(this.limitValue) + 100;
        },
        saveAnbaoBox(){
            var _this = this;
            this.btnDel = false;
            var postData = {
                account_id: accountId,
                box_name: (this.gameTitle === 'anbao') ? '十人暗宝' : '',
                game_type: returnGameType(this.gameTitle),
                data: {
                    data_key:Date.parse(new Date())+randomString(5),
                    chip_type:this.chip_type,
                    ticket_count: Number((this.gameTitle === 'anbao') ? (this.ticket_count===0 ? 2 : 4) : ''),
                    banker_mode: this.banker_mode + 1,
                    upper_limit: Number(this.limitValue),
                    first_lossrate:this.anbaoProperty.first_lossrate[this.first_lossrate],
                    second_lossrate:this.anbaoProperty.second_lossrate[this.second_lossrate],
                    three_lossrate:this.anbaoProperty.three_lossrate[this.three_lossrate],
                    game_type: returnGameType(this.gameTitle),
                    countDown: (function(){
                        return _this.countDown.map(function(value, index) {
                            return Number(value);
                        })
                    })()
                }
            }
            
            this.$http.post('/box/addBox', JSON.stringify(postData)).then(function(returnValue){
                var result = returnValue.data.result;
                if(result===0){
                    var message = returnValue.data.result_message;
                    _this.$emit('anbaoReturn', message);
                }else if(result===-1){
                    var message = returnValue.data.result_message;
                    _this.$emit('anbaoReturn', message);
                }
            }).catch(function(){
                console.log('出错了');
            })
        }
    },
    beforeMount: function(){
        this.btnDel = true;
        console.log(this.btnDel)
    }

}

