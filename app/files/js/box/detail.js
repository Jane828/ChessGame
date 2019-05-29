var boxDetail = {
    name: 'boxDetail',
    props: ['boxLen', 'result'],
    template: `
        <div class="detail-wrapper">
            <sg-detail v-if="gameDetail==='sg'" :enable="enable" :sgInfo="sgInfo" @cancelDetail="cancelDetail" @checkData="checkData"></sg-detail>
            <flower-detail v-if="gameDetail==='flower'" :enable="enable" :flowerInfo="flowerInfo" @cancelDetail="cancelDetail" @checkData="checkData"></flower-detail>
            <bull-detail v-if="gameDetail==='bull'" :enable="enable" :bullInfo="bullInfo" @cancelDetail="cancelDetail" @checkData="checkData"></bull-detail>
	    <anbao-detail v-if="gameDetail==='anbao'" :enable="enable" :anbaoInfo="anbaoInfo" @cancelDetail="cancelDetail" @checkData="checkData"></anbao-detail>
            <div v-if="showSave" class="save" ref="save">保存成功</div>
            <div v-if="delShow" class="save" >解散包厢成功</div>
            <div v-if="alert" class="alert">{{alertMsg}}</div>
            <div class="disbandbox-mask" v-show="mask">
                <div class="disbandmask-container">
                <div class="disbandmask-show">
                    <div class="disbandmask-header">
                        <img :src="baseUrl+'storetitle.png'" alt="解散包厢">
                        <span>设置</span>
                    </div>
                    <div class="disbandmask-border">
                        <img :src="baseUrl+'settingback.png'"> 
                        <span>是否确认解散包厢</span>
                    </div>
                    <div class="disbandmask-btn">
                        <img :src="baseUrl+'ok.png'" alt="确定" @click="delBox " v-if="btnDel">
                        <img :src="baseUrl+'ok.png'" alt="确定"  v-if="!btnDel">
                        <img :src="baseUrl+'cancel.png'" alt="取消" @click="maskCancel">
                    </div>
                </div>
                </div>
            </div>
            <div class="box-last" @click="lastBox" v-show="lastShow"></div>
            <div class="box-next" @click="nextBox" v-show="nextShow"></div>
            <div class="noBox-wrap" v-if="boxList.length===0">
                <div class="noBox" >
                    暂无包厢
                </div>
            </div>    
            <div class="box-show" v-if="boxList.length!==0">
                <div class="box-container clearfloat" ref="boxContainer" @touchstart="touchStart" @touchmove="touchMove" @touchend="touchEnd">
                    <div class="inner-box" v-for="item in boxList">
                        <div class="innerbox-img">
                            <img :src="baseUrl+'storetitle.png'">
                            <span>房间详情</span>
                        </div>
                        <div class="innerbox-property">
                            <span class="fix-property" @click="fixBoxProperty">修改包厢属性</span>
                            <span class="disband-box" @click="disbandbox">解散包厢</span>
                        </div>
                        <div class="innerbox-show">
                            <div class="box-options">
                                <div class="options-left">
                                    <p>创建者：{{item.nickname}}</p>
                                    <p>房间数：{{item.room_count}}</p>
                                </div>
                                <div class="options-right">
                                    <p>包厢号：{{item.box_number}}</p>
                                    <p>包厢名：{{item.box_name}}</p>
                                </div>
                            </div>
                            <div class="options-show">
                                <img :src="baseUrl+'settingback.png'" class="options-img">
                                <div class="options-params">
                                    <!--飘三叶的底分-->
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('default_score')">
                                        <span>底分：</span>
                                        <span>{{JSON.parse(item.config).default_score}}分</span>
                                    </div>
                                    <!--有筹码显示筹码-->
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('chip_type')&&item.game_type!=='61'">
                                        <span>筹码：</span>
                                        <span v-for="item in JSON.parse(item.config).chip_type">{{item}}/{{item*2}}</span>
                                    </div>
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('chip_type')&&item.game_type==='61'">
                                        <span>筹码：</span>
                                        <span>10、20、30、50、100</span>
                                    </div>
                                    <!--三公的模式-->
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('banker_mode')&&item.game_type!=='61'">
                                        <span>模式：</span>
                                        <span>{{(JSON.parse(item.config).banker_mode === 1) ? '自由抢庄' : 
                                              (JSON.parse(item.config).banker_mode===2) ? '明牌抢庄' : 
                                              (JSON.parse(item.config).banker_mode===3) ? '牛牛上庄' : 
                                              (JSON.parse(item.config).banker_mode===4) ? '通比牛牛' : 
                                              (JSON.parse(item.config).banker_mode===5) ? '固定庄家' : ''}}</span>
                                    </div>
                                    <!--暗宝的模式-->
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('banker_mode')&&item.game_type==='61'">
                                        <span>模式：</span>
                                        <span>{{(JSON.parse(item.config).banker_mode === 1) ? '自由抢庄' : 
                                              (JSON.parse(item.config).banker_mode===2) ? '固定庄家' : 
                                              (JSON.parse(item.config).banker_mode===3) ? '开房做庄' : ''}}</span>
                                    </div>
                                    <!--三公的底分-->
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('score_type')&&(item.game_type==='36'||item.game_type==='37')">
                                        <span>底分：</span>
                                        <span>{{JSON.parse(item.config).score_type}}分</span>
                                    </div>
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('score_type')&&(item.game_type!=='36'&&item.game_type!=='37')">
                                        <span>底分：</span>
                                        <span>{{(JSON.parse(item.config).score_type!==7) ? bullScore[JSON.parse(item.config).score_type] : JSON.parse(item.config).score_value}}分</span>
                                    </div>
                                    <!--三公的规则-->
                                    <div v-show="(JSON.parse(item.config).hasOwnProperty('is_bj')&&JSON.parse(item.config).hasOwnProperty('is_joker'))&&(JSON.parse(item.config).is_bj!==0||JSON.parse(item.config).is_joker!==0)">
                                        <div>
                                            <span style="font-weight:bold;">规则：</span>
                                            <span>{{JSON.parse(item.config).is_joker===1 ? '天公x10-雷公x7-地公x5':'' }}</span>
                                        </div>
                                        <div>
                                            <span  style="opacity:0;" >规则：</span>
                                            <span>{{JSON.parse(item.config).is_bj===1 ? '暴玖x9':'' }}</span>
                                        </div>
                                    </div>
                                    <!--飘三叶的看牌-->
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('seenProgress')">
                                        <span>看牌：</span>
                                        <span>积分池低于{{JSON.parse(item.config).seenProgress}}分不能看牌</span>
                                    </div>
                                    <!--飘三叶比牌-->
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('compareProgress')">
                                        <div>
                                            <span style="font-weight:bold;">比牌：</span>
                                            <span v-show="JSON.parse(item.config).hasOwnProperty('raceCard')">
                                                {{(JSON.parse(item.config).raceCard) ? '' : '首轮禁止比牌' }}
                                            </span>
                                        </div>
                                        <div>
                                            <span style="opacity:0;">比牌：</span>
                                            <span>积分池低于{{JSON.parse(item.config).compareProgress}}分不能比牌</span>
                                        </div>
                                    </div>
                                    <!--局数只有飘三叶和暗宝是 ticket_count，其他的都是ticket_type-->
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('ticket_count')&&item.game_type!=='61'">
                                        <span>局数：</span>
                                        <span>{{(item.game_type==='110') ? 
                                                ((JSON.parse(item.config).ticket_count===2) ? '10局x2张房卡' : '20局x4张房卡') : 
                                                ((JSON.parse(item.config).ticket_count===1) ? '10局x1张房卡' : '20局x2张房卡')
                                            }}</span>
                                    </div>
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('ticket_count')&&item.game_type==='61'">
                                        <span>局数：</span>
                                        <span>{{(JSON.parse(item.config).ticket_count===2) ? '12局x2张房卡' : '24局x4张房卡'}}</span>
                                    </div>
                                    <!--牛牛的牌型-->
                                    <div v-if="item.game_type==='5' || item.game_type==='9' || item.game_type==='71' || item.game_type==='12' || item.game_type==='13'">
                                        <div>
                                            <span style="font-weight:bold;">牌型：</span>
                                            <span v-if="JSON.parse(item.config).hasOwnProperty('has_ghost')">
                                                {{JSON.parse(item.config).has_ghost === 1 ? '有癞子' : ''}}
                                            </span>
                                            <span v-if="JSON.parse(item.config).hasOwnProperty('is_cardfour')">
                                                {{JSON.parse(item.config).is_cardfour === 1 ? '四花牛(4倍)' : ''}}
                                            </span>
                                        </div>    
                                        <div>
                                            <span style="opacity:0;">牌型：</span>
                                            <span v-if="JSON.parse(item.config).hasOwnProperty('is_cardfive')">
                                                {{JSON.parse(item.config).is_cardfive === 1 ? '五花牛(5倍)' : ''}}
                                            </span>
                                            <span v-if="JSON.parse(item.config).hasOwnProperty('is_straight')">
                                                {{JSON.parse(item.config).is_straight === 1 ? '顺子牛(6倍)' : ''}}
                                            </span>
                                        </div>
                                        <div>
                                            <span style="opacity:0;">牌型：</span>
                                            <span v-if="JSON.parse(item.config).hasOwnProperty('is_flush')">
                                                {{JSON.parse(item.config).is_flush === 1 ? '同花牛(6倍)' : ''}}
                                            </span>
                                            <span v-if="JSON.parse(item.config).hasOwnProperty('is_hulu')">
                                                {{JSON.parse(item.config).is_hulu === 1 ? '葫芦牛(6倍)' : ''}}
                                            </span>
                                        </div>
                                        <div>
                                            <span style="opacity:0;">牌型：</span>
                                            <span v-if="JSON.parse(item.config).hasOwnProperty('is_cardbomb')">
                                                {{JSON.parse(item.config).is_cardbomb === 1 ? '炸弹牛(6倍)' : ''}}
                                            </span>
                                            <span v-if="JSON.parse(item.config).hasOwnProperty('is_straightflush')">
                                                {{JSON.parse(item.config).is_straightflush === 1 ? '同花顺(7倍)' : ''}}
                                            </span>
                                        </div>
                                        <div>
                                            <span style="opacity:0;">牌型：</span>
                                            <span v-if="JSON.parse(item.config).hasOwnProperty('is_cardtiny')">
                                                {{JSON.parse(item.config).is_cardtiny === 1 ? '五小牛(8倍)' : ''}}
                                            </span>
                                        </div>
                                    </div>
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('ticket_type')">
                                        <span>局数：</span>
                                        <span>{{(item.game_type==='37') ? 
                                                ((JSON.parse(item.config).ticket_type===1) ? '10局x2张房卡' : '20局x4张房卡') : 
                                                (item.game_type==='36' || item.game_type=== '5' ) ? 
                                                ((JSON.parse(item.config).ticket_type===1) ? '10局x1张房卡' : '20局x2张房卡') : 
                                                (JSON.parse(item.config).ticket_type===1) ? '12局x2张房卡' : '24局x4张房卡'
                                            }}</span>
                                    </div>
                                    <div v-if="item.game_type==='5' || item.game_type==='9' || item.game_type==='71' || item.game_type==='12' || item.game_type==='13'">
                                        <span>倍数：</span>
                                        <span >
                                            {{JSON.parse(item.config).times_type===1 ? '1,2,4,5' : 
                                            JSON.parse(item.config).times_type===2 ? '1,3,5,8' : '2,4,6,10'}}
                                        </span>
                                    </div>
                                    <div v-if="(item.game_type==='5' || item.game_type==='9' || item.game_type==='71' || item.game_type==='12' || item.game_type==='13')&&(JSON.parse(item.config).banker_mode===5)">
                                        <span>上庄：</span>
                                        <span >
                                            {{JSON.parse(item.config).banker_score_type===1 ? '无' : 
                                            JSON.parse(item.config).banker_score_type===2 ? '300' : 
                                            JSON.parse(item.config).banker_score_type===3 ? '500' : '1000'}}
                                        </span>
                                    </div>
                                    <!--暗宝的赔率-->
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('first_lossrate')">
                                        <div>
                                            <span style="font-weight: bold;">赔率：</span>
                                            <span>龙、虎/出、入：&nbsp;&nbsp;1:{{JSON.parse(item.config).first_lossrate}}</span>
                                        </div>
                                        <div>
                                            <span style="opacity:0;">赔率：</span>
                                            <span>同、粘：&nbsp;&nbsp;1:{{JSON.parse(item.config).second_lossrate}}</span>
                                        </div>
                                        <div>
                                            <span style="opacity:0;">赔率：</span>
                                            <span>角、串：&nbsp;&nbsp;1:{{JSON.parse(item.config).three_lossrate}}</span>
                                        </div>
                                    </div>
                                    <!--飘三叶的上限-->
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('upper_limit')">
                                        <span>上限：</span>
                                        <span>{{JSON.parse(item.config).upper_limit}}</span>
                                    </div>
                                    <!--飘三叶的喜牌-->
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('extraRewards')">
                                        <span>喜牌：</span>
                                        <span>{{JSON.parse(item.config).extraRewards}}</span>
                                    </div>
                                    <!--特殊235吃豹子，大牌飘三叶没有这个-->
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('allow235GTPanther')">
                                        <span>特殊：</span>
                                        <span>{{(JSON.parse(item.config).allow235GTPanther===0) ? '不允许235吃豹子' : '允许235吃豹子'}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-operator">
                <img :src="baseUrl+'stop.png'" alt="暂停" v-if="stop" @click="toStop">
                <img :src="baseUrl+'start.png'" alt="开始" v-else @click="toStart">
                <img :src="baseUrl+'joinbox.png'" alt="进入包厢" @click="toJoinBox">
                <img :src="baseUrl+'porkerscore.png'" alt="牌桌情况" @click="showTable">
            </div>
        </div>
    `,
    data: function() {
        return {
            baseUrl: baseUrl+'files/images/box/',
            boxIndex: 0,
            startPageX: 0,
            endPageX: 0,
            innerW: 0,
            innerH: 0,
            ismove: false,
            mask: false,
            lastShow: false,
            nextShow: false,
            stop: false,
            showSave: false,
            boxLength: this.boxLen,
            boxList: this.result, 
            delShow: false,
            enable: true,
            bullInfo:null,
            flowerInfo: null,
            sgInfo:null,
	        anbaoInfo: null,
            gameDetail: '',
            bullScore: [0,1,3,5,10,20,2],
            alertMsg: '',
            alert:false,
            messageTime: 2000,
            btnDel: true
        }
    },
    methods: {
        showAlert(msg){
            var _this = this;
            this.alertMsg = msg;
            this.alert = true;
            setTimeout(function(){
                _this.alert = false;
            },_this.messageTime);
        },
        lastBox() {
            this.boxIndex--;
            if(this.boxIndex === 0){
                this.lastShow = false;
                this.nextShow = true;
                this.$refs.boxContainer.style.left = -80*(this.boxIndex) + 'vw';
                return;
            }
            this.$refs.boxContainer.style.left = -80*(this.boxIndex) + 'vw';
            if((this.boxIndex>0)&&(this.boxIndex<this.boxLength)){
                this.lastShow = true;
                this.nextShow = true;
            }
        },
        nextBox() {
            this.boxIndex++;
            if(this.boxIndex === this.boxLength) {
                this.nextShow = false;
                this.lastShow = true;
                this.$refs.boxContainer.style.left = -this.boxIndex*80 + 'vw'; 
                return;
            };
            this.$refs.boxContainer.style.left = -this.boxIndex*80 + 'vw'; 
            if((this.boxIndex>0)&&(this.boxIndex<this.boxLength)){
                this.lastShow = true;
                this.nextShow = true;
            }
        },
        cancelDetail() {
          this.gameDetail = '';  
        },
        touchStart(e) {
            //确定有一个手指
            this.ismove = false;
            if(e.targetTouches.length === 1){
                this.startPageX = e.targetTouches[0].pageX;
            }
        },
        checkData(data){
            this.boxList[this.boxIndex].config = JSON.stringify(data);
            this.cancelDetail();
        },
        fixBoxProperty(){
            var info = this.boxList[this.boxIndex];
            var game_type = Number(info.game_type);
            if(game_type===5||game_type===9||game_type===12||game_type===13||game_type===71){
                this.bullInfo = info;
                this.gameDetail = 'bull';
            }else if(game_type===36||game_type===37){
                this.sgInfo = info;
                this.gameDetail = 'sg';
            }else if(game_type===1||game_type===110||game_type===111){
                this.flowerInfo = info;
                this.gameDetail = 'flower';
            }else if(game_type===61){
                this.anbaoInfo = info;
                this.gameDetail = 'anbao';
            }
	},
        touchMove(e) {
            var difx;
            var leftX;
            this.ismove = true;
            if(e.targetTouches.length === 1){
                this.endPageX = e.targetTouches[0].pageX;
                difx = this.endPageX - this.startPageX; //向左滑动是小于0， 向右滑动大于0
                leftX = window.getComputedStyle(this.$refs.boxContainer, null).left || this.$refs.currentStyle.left;
                leftX = leftX.slice(0, leftX.indexOf('px'));
                //console.log(leftX)
                this.$refs.boxContainer.style.left = Number(leftX) + Number(difx) + 'px';
            }
        },
        touchEnd(e) {
            var difx = this.startPageX - this.endPageX;
            //只有移动距离大于10并且触发touchmove事件才可以
            // 向右
            console.log(difx)
            if((difx <= 0) && this.ismove ){
                if(this.boxLength===0){
                    this.$refs.boxContainer.style.left = 0+'px';
                }else if(this.boxIndex===0){
                    this.$refs.boxContainer.style.left = 0+'px';
                } else{
                    this.boxIndex--;
                    if(this.boxIndex===0){
                        this.lastShow = false;
                        this.nextShow = true;
                    }else{
                        this.lastShow = true;
                        this.nextShow = true
                    }
                    this.$refs.boxContainer.style.left = -this.boxIndex*80+'vw';
                }
                // 向左
            }else if((difx>=0) && this.ismove) {
                if(this.boxLength===0){
                    this.$refs.boxContainer.style.left = 0+'px';
                }else if(this.boxLength===this.boxIndex){
                    this.$refs.boxContainer.style.left = -this.boxLength*80+'vw';
                }else {
                    this.boxIndex++;
                    if(this.boxIndex===this.boxLength){
                        this.lastShow = true;
                        this.nextShow = false;
                    }else{
                        this.lastShow = true;
                        this.nextShow = true;
                    }
                    this.$refs.boxContainer.style.left = -this.boxIndex*80+'vw';
                }
            }
        },
        disbandbox(){
            this.mask = true;
            this.btnDel = true;
        },
        maskCancel() {
            this.mask = false;
        },
        toStart() {
            if(this.boxList.length === 0) return;
            var _this = this;
            var postData = {
                account_id: accountId,
                box_id: this.boxList[this.boxIndex].box_id,
                box_number:this.boxList[this.boxIndex].box_number,
                status: 1
            }
            this.$http.post('/box/setBoxStatus', JSON.stringify(postData)).then(function(returnValue){
                var data = returnValue.body;
                console.log(data);
                if(data.result===0){
                    this.showSave = true;
                    if(data.status === 1){
                        _this.stop = true;
                        _this.boxList[_this.boxIndex].status="1";
                    }
                }
            }).catch(function(err){
                console.log('出错了');
            })
            setTimeout(function(){
                _this.showSave = false;
            }, _this.messageTime);
        },
        toStop() {
            if(this.boxList.length === 0 ) return;
            var _this = this;
            var postData = {
                account_id: accountId,
                box_id: this.boxList[this.boxIndex].box_id,
                box_number:this.boxList[this.boxIndex].box_number,
                status: 0
            }
            this.$http.post('/box/setBoxStatus', JSON.stringify(postData)).then(function(returnValue){
                var data = returnValue.body;
                console.log(data)
                if(data.result===0){
                    this.showSave = true;
                    if(data.status === 0){
                        _this.stop = false;
                        _this.boxList[_this.boxIndex].status="0";
                    }
                }
            }).catch(function(err){
                console.log('出错了');
            })
            setTimeout(function(){
                _this.showSave = false;
            }, _this.messageTime);
        },
        showTable() {
            if(this.boxList.length === 0) return;
            var postData = {
                account_id: accountId,
                box_id: this.boxList[this.boxIndex].box_id,
                box_number:this.boxList[this.boxIndex].box_number
            }
            console.log(postData)
            this.$http.post('/box/boxCondition', JSON.stringify(postData)).then(function(returnValue){
                var result = returnValue.body.data;
                console.log(result)
                this.$emit('showTable', result);
            })
        },
        delBox(){
            var _this = this;
            this.btnDel = false;
            var postData = {
                account_id: accountId,
                box_id: this.boxList[this.boxIndex].box_id,
                box_number: this.boxList[this.boxIndex].box_number
            }
            this.$http.post('/box/delBox', JSON.stringify(postData))
                      .then(function(returnValue){
                          this.boxList.splice(this.boxIndex, 1);
                          console.log(this.boxList.length)
                          _this.$refs.boxContainer.style.width=_this.boxList.length*80 +'vw';
                          if(_this.boxLength === _this.boxIndex) {
                              _this.boxIndex--;
                            _this.$refs.boxContainer.style.left = -_this.boxIndex*80 + 'vw';
                          }
                          _this.checkBoxStatus(_this.boxIndex);
                          _this.boxLength = _this.boxLength - 1;
                          console.log(_this.boxLength, _this.boxIndex);
                          if(_this.boxLength===0){
                              _this.nextShow = false;
                              _this.lastShow = false;
                          }
                          if(_this.boxLength>0 && _this.boxIndex<_this.boxLength){
                              _this.lastShow = true;
                              _this.nextShow = true;
                          }
                          if(_this.boxLength>0 && _this.boxIndex===_this.boxLength){
                                _this.lastShow = true;
                                _this.nextShow = false;
                          }
                          if(_this.boxLength>0 && _this.boxIndex===0){
                            _this.lastShow = false;
                            _this.nextShow = true;
                          }
                          _this.mask = false;
                          var data = returnValue.body;
                          if(data.result===0){
                              _this.delShow=true;
                              setTimeout(function(){
                                  _this.delShow = false;
                              },_this.messageTime)
                          }
                      })
                      .catch(function(){
                          console.log('出错了');
                      })
        },
        checkBoxStatus(index){
            if(index===-1) return;
            if(this.boxList[index].status==="0"){
                this.stop = false;
            }else{
                this.stop = true;
            }
        },
        toJoinBox(){
            if(this.boxList.length===0) return;
            var _this = this;
            var box = this.boxList[this.boxIndex];
            var game_type = box.game_type;
            var url = returnGameWsUrl(game_type);
            var wsObj = {
                operation: 'JoinBox',
                account_id: accountId,
                session: session,
                data: {
                    box_number: box.box_number,
                    game_type: game_type
                }
            }
            console.log(wsObj, game_type);
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
                if(Number(obj.result) === -1){
                    _this.showAlert(obj.result_message);
                    return;
                }else if(Number(obj.result) === 0){
                    if(Number(game_type) === 5){
                        window.location.href = baseUrl + 'f/b?i=' + obj.data.room_number + '_';
                    }else if(Number(game_type) === 9){
                        window.location.href = baseUrl + 'f/nb?i=' + obj.data.room_number + '_';
                    }else if(Number(game_type) === 71){
                        window.location.href = baseUrl + 'f/lb?i=' + obj.data.room_number + '_';
                    }else if(Number(game_type) === 12){
                        window.location.href = baseUrl + 'f/tb?i=' + obj.data.room_number + '_';
                    }else if(Number(game_type) === 13){
                        window.location.href = baseUrl + 'f/fb?i=' + obj.data.room_number + '_';                   
                    }else if(Number(game_type) === 36){
                        window.location.href = baseUrl + 'f/sg?i=' + obj.data.room_number + '_';
                    }else if(Number(game_type) === 37){
                        window.location.href = baseUrl + 'f/nsg?i=' + obj.data.room_number + '_';
                    }else if(Number(game_type) === 1){
                        window.location.href = baseUrl + 'f/yf?i=' + obj.data.room_number + '_';            
                    }else if(Number(game_type) === 110){
                        window.location.href = baseUrl + 'f/tf?i=' + obj.data.room_number + '_';
                    }else if(Number(game_type) === 111){
                        window.location.href = baseUrl + 'f/bf?i=' + obj.data.room_number + '_';
                    }else if(Number(game_type) === 61){
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
                    toJoinBox();
                }else {
                    return 0;
                }
            }
            ws.onerror = function(err){
                console.log('出错了');
            }
        }
    },
    watch:{
        boxIndex: function(newIndex, oldIndex){
            this.checkBoxStatus(newIndex);
        }
    },
    beforeMount(){
        if(this.boxLength > 0){
            if(this.boxList[0].status==="0"){
                this.stop = false;
            }else{
                this.stop = true;
            }
        }
    },
    mounted: function(){
        var _this = this;
        this.$nextTick(function() {
            _this.innerW = window.innerWidth;
            _this.innerH = window.innerHeight;
            if(_this.boxLength>0){
                 console.log("_this.boxLength:==========");
                 console.log(_this.boxLength)
                 _this.nextShow = true;                
                _this.$refs.boxContainer.style.width=(_this.boxLength+1)*80+'vw';
            }

        })       
    },
    components: {
        'bullDetail': bullDetail,
        'flowerDetail': flowerDetail,
        'sgDetail': sgDetail,
	'anbaoDetail': anbaoDetail
    }
}
