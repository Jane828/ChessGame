var boxDetail = {
    name: 'boxDetail',
    props: ['boxLen', 'result'],
    template: `
        <div class="detail-wrapper">
            <div v-if="showSave" class="save" ref="save">保存成功</div>
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
                        <img :src="baseUrl+'ok.png'" alt="确定">
                        <img :src="baseUrl+'cancel.png'" alt="取消" @click="maskCancel">
                    </div>
                </div>
                </div>
            </div>
            <div class="box-last" @click="lastBox" v-show="lastShow"></div>
            <div class="box-next" @click="nextBox" v-show="nextShow"></div>
            <div class="box-show">
                <div class="box-container clearfloat" ref="boxContainer" @touchstart="touchStart" @touchmove="touchMove" @touchend="touchEnd">
                    <div class="inner-box" v-for="item in boxList">
                        <div class="innerbox-img">
                            <img :src="baseUrl+'storetitle.png'">
                            <span>房间详情</span>
                        </div>
                        <div class="innerbox-property">
                            <span class="fix-property">修改包厢属性</span>
                            <span class="disband-box" @click="disbandbox">解散包厢</span>
                        </div>
                        <div class="innerbox-show">
                            <div class="box-options">
                                <div class="options-left">
                                    <p>创建者：daldjasjldjalskdjlkka</p>
                                    <p>当前人数</p>
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
                                        <span>{{JSON.parse(item.config).default_score}}</span>
                                    </div>
                                    <!--有筹码显示筹码-->
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('chip_type')">
                                        <span>筹码：</span>
                                        <span v-for="item in JSON.parse(item.config).chip_type">{{item}}/{{item*2}}  </span>
                                    </div>
                                    <!--三公的模式-->
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('banker_mode')">
                                        <span>模式：</span>
                                        <span>{{(JSON.parse(item.config).banker_mode === 1) ? '自由抢庄' : 
                                              (JSON.parse(item.config).banker_mode===2) ? '明牌抢庄' : ''}}</span>
                                    </div>
                                    <!--三公的底分-->
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('score_type')">
                                        <span>底分：</span>
                                        <span>{{JSON.parse(item.config).score_type}}分</span>
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
                                    <!--飘三叶局数 ticket_count只有飘三叶有其他的都是ticket_type-->
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('ticket_count')">
                                        <span>局数：</span>
                                        <span>{{(item.game_type==='110') ? 
                                                ((JSON.parse(item.config).ticket_count===2) ? '10局x2张房卡' : '20局x4张房卡') : 
                                                ((JSON.parse(item.config).ticket_count===1) ? '10局x1张房卡' : '20局x2张房卡')
                                            }}</span>
                                    </div>
                                    <div v-show="JSON.parse(item.config).hasOwnProperty('ticket_type')">
                                        <span>局数：</span>
                                        <span>{{(item.game_type==='37') ? 
                                                ((JSON.parse(item.config).ticket_type===1) ? '10局x2张房卡' : '20局x4张房卡') : 
                                                (item.game_type==='36') ? 
                                                ((JSON.parse(item.config).ticket_type===1) ? '10局x1张房卡' : '20局x2张房卡') : ''
                                            }}</span>
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
                <img :src="baseUrl+'stop.png'" alt="暂停" v-if="stop" @click="toStart">
                <img :src="baseUrl+'start.png'" alt="开始" v-else @click="toStop">
                <img :src="baseUrl+'joinbox.png'" alt="进入包厢">
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
            boxLen: this.boxLen,
            boxList: this.result, 
        }
    },
    methods: {
        lastBox() {
            this.boxIndex--;
            if(this.boxIndex === 0){
                this.lastShow = false;
                this.nextShow = true;
                this.$refs.boxContainer.style.left = -80*(this.boxIndex) + 'vw';
                return;
            }
            this.$refs.boxContainer.style.left = -80*(this.boxIndex) + 'vw';
            if((this.boxIndex>0)&&(this.boxIndex<this.boxLen)){
                this.lastShow = true;
                this.nextShow = true;
            }
        },
        nextBox() {
            this.boxIndex++;
            if(this.boxIndex === this.boxLen) {
                this.nextShow = false;
                this.lastShow = true;
                this.$refs.boxContainer.style.left = -this.boxIndex*80 + 'vw'; 
                return;
            };
            this.$refs.boxContainer.style.left = -this.boxIndex*80 + 'vw'; 
            if((this.boxIndex>0)&&(this.boxIndex<this.boxLen)){
                this.lastShow = true;
                this.nextShow = true;
            }
        },
        touchStart(e) {
            //确定有一个手指
            this.ismove = false;
            if(e.targetTouches.length === 1){
                this.startPageX = e.targetTouches[0].pageX;
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
            if((difx <= 0) && this.ismove ){
                if(this.boxLen===0){
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
            }else if((difx > 0) && this.ismove) {
                if(this.boxLen===0){
                    this.$refs.boxContainer.style.left = 0+'px';
                }else if(this.boxLen===this.boxIndex){
                    this.$refs.boxContainer.style.left = -this.boxLen*80+'vw';
                }else {
                    this.boxIndex++;
                    if(this.boxIndex===this.boxLen){
                        this.lastShow = true;
                        this.nextShow = false;
                    }else{
                        this.lastShow = true;
                        this.nextShow = true
                    }
                    this.$refs.boxContainer.style.left = -this.boxIndex*80+'vw';
                }
            }
        },
        disbandbox(){
            this.mask = true;
        },
        maskCancel() {
            this.mask = false;
        },
        toStart() {
            var _this = this;
            this.showSave = true;
            this.stop = false;
            setTimeout(function(){
                _this.showSave = false;
            }, 500);
        },
        toStop() {
            var _this = this;
            this.showSave = true;
            this.stop = true;
            setTimeout(function(){
                _this.showSave = false;
            }, 500);
        },
        showTable() {
            var postData = {
                account_id: accountId,
                box_id: this.boxList[this.boxIndex].box_id,
                box_number:this.boxList[this.boxIndex].box_number
            }
            console.log(postData)
            this.$http.post('/box/boxCondition', JSON.stringify(postData)).then(function(data){
                console.log(data);
            })
        }
    },
    mounted: function(){
        var _this = this;
        this.$nextTick(function() {
            _this.innerW = window.innerWidth;
            _this.innerH = window.innerHeight;
            if(_this.boxLen!==0){
                 _this.nextShow = true;
                _this.$refs.boxContainer.style.width=(_this.boxLen+1)*80+'vw';
            }
        })       
    }
}