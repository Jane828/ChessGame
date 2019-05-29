var boxMain = {
    name: 'boxMain',
    template:`
    <div class="main-wrap">
        <div class="createSg-result" v-show="sgResult">{{sgMessage}}</div>
        <div class="createFlower-result" v-show="flowerResult">{{flowerMessage}}</div>
        <div class="createBull-result" v-show="bullResult">{{bullMessage}}</div>
        <div class="createAnbao-result" v-show="anbaoResult">{{anbaoMessage}}</div>
        <div class="box-tips">
            <img :src="baseUrl+'tips.png'" alt="管理利器" class="tips-img">
        </div>
        <div class="box-create">
            <img :src="baseUrl+'box.png'" alt="包厢" class="box-img" @click="gotoDetail">
            <img :src="baseUrl+'createbox.png'" alt="创建房间" class="box-img" @click="toCreateRoom">
        </div>
        <div class="box-area">
            <img :src="baseUrl+'boxarea.png'" alt="包厢区" class="boxarea-img">
            <div class="boxarea-setting">
                <img :src="baseUrl+'boxsetting.png'" alt="操作" class="boxsetting-img"></img>
                <!--<img :src="baseUrl+'setting.png'" alt="操作" class="boxsetting-btn" @click="showSetting"></img>-->
                <img :src="baseUrl+'freash.png'" alt="刷新" class="freash-btn" @click="freashRoom">
            </div>
            <!--<a href="#" :style="linkStyle" class="freshen-link" @click="freshen"><img :src="baseUrl+'freshen.png'" alt="刷新" class="box-freshen"></a>-->
            <div class="game-select">
                <img :src="baseUrl+'gamelast.png'" class="game-last">
                <div class="game-list">
                    <div class="game-show">
                        <div class="game-item" v-for="item in games" @click="toCurrent(item.index)">
                            <img :src="baseUrl+'currentgameback.png'" alt="" class="game-back" v-if="item.current">
                            <img :src="baseUrl+'gameback.png'" alt="" class="game-back" v-else>
                            <span>{{item.name}}</span>
                        </div>
                    </div>
                </div>
                <img :src="baseUrl+'gamenext.png'" class="game-next">
            </div>
            <div class="noRoom" v-if="commonBoxStruct.length===0&&commonRoom.length===0">暂无房间</div>
            <div class="forbiden-float" v-show="zoneShow" >
                <div class="zone-wrap" ref="zoneWrap">
                    <div class="box-zone" v-for="item in commonBoxStruct">
                        <img :src="baseUrl+'boxicon.png'" class="box-icon">
                        <p class="zone-nickname">{{item.nickname}}<p>
                        <p class="zone-title">{{item.box_name}}</p>
                        <div class="zone-join" @click="quickJoin(item.box_number, item.game_type)">快速加入</div>
                        <p class="zone-room">包厢号：{{item.box_number}}</p>
                        <div class="zone-detail">
                            <div class="zone-detail-wrap" @click="showDetail(item.box_id, item.box_number, item.account_id)">
                                <img :src="baseUrl+'detail.png'" class="zone-detailimg">
                                <span>详情</span>
                            </div>
                        </div>
                    </div>
                    <div class="box-zone" v-for="item in commonRoom">
                        <p class="zone-nickname">{{item.nickname}}<p>
                        <p class="zone-title">{{returnGameName(item.game_type)}}</p>
                        <div class="zone-join">
                            <div class="room-count">
                                <img :src="baseUrl+'porkerNum.png'" alt="局数" >
                                <span>{{item.gnum}}/{{item.total_num}}</span>
                            </div>
                            <div class="room-people">
                                <img :src="baseUrl+'people.png'" alt="人数">
                                <span>{{item.user_count}}/{{item.player_max_num}}</span>
                            </div>
                        </div>
                        <p class="zone-room">房号：{{item.room_number}}</p>
                        <p class="zone-time">时间：{{item.create_time}}</p>
                        <div class="zone-detail">
                            <div class="zone-detail-wrap"   @click="showRoomDetail(item.room_id, item.game_type, item.room_number)">
                                <img :src="baseUrl+'detail.png'" class="zone-detailimg">
                                <span>详情</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>    
        </div>
        <div class="createRoom-mask" v-show="createRoom">
            <div class="createRoom-area">
            <img :src="baseUrl+'close.png'" class="createRoom-close" @click="closeCreateRoom">
                <div class="createRoom-title">
                    <img :src="baseUrl+'storetitle.png'" alt="选择游戏">
                    <span>选择游戏</span>
                </div>
                <div class="createRoom-show">
                    <div class="img-wrap" v-for="item in gameImgs" @click="gameList(item.sort)">
                        <img :src="item.url" alt="飘三叶">
                    </div>
                </div>
            </div>
        </div>
        <div class="selectgame-mask" v-show="selectGame">
            <div class="selectgame-area">
            <img :src="baseUrl+'close.png'" class="selectgame-close" @click="closeSelectGame">
                <div class="selectgame-title">
                    <img :src="baseUrl+'storetitle.png'" alt="选择游戏">
                    <span>选择游戏</span>
                </div>
                <div class="selectgame-show">
                    <div class="img-wrap" v-for="item in listGames.sort" @click="showGameProperty(item.sort, item.title)">
                        <img :src="item.url" alt="飘三叶">
                    </div>
                </div>
            </div>    
        </div>
        <flower-p v-if="gameProperty.flower" :gameTitle="gameProperty.game" @cancelFlowerP="cancelFlowerP" @flowerReturn="flowerReturn"></flower-p>
        <bull-p v-if="gameProperty.bull" @cancelBullP="cancelBullP" :gameTitle="gameProperty.game" @bullReturn="bullReturn"></bull-p>
        <sg-p v-if="gameProperty.sg" @cancelSgP="cancelSgP" :gameTitle="gameProperty.game" @sgReturn="sgReturn"></sg-p>
        <anbao-P v-if="gameProperty.anbao" @cancelAnbaoP="cancelAnbaoP" :gameTitle="gameProperty.game" @anbaoReturn="anbaoReturn"></anbao-P>
        <sg-detail v-if="gameDetail==='sg'" :enable="enable" :sgInfo="sgInfo" @cancelDetail="cancelDetail" :isRoomOrBox="isRoomOrBox" :roomNumber="room_number" @showAl="showAl"></sg-detail>
        <flower-detail v-if="gameDetail==='flower'" :enable="enable" :flowerInfo="flowerInfo" @cancelDetail="cancelDetail" :isRoomOrBox="isRoomOrBox" :roomNumber="room_number" @showAl="showAl"></flower-detail>
        <bull-detail v-if="gameDetail==='bull'" :enable="enable" :bullInfo="bullInfo" @cancelDetail="cancelDetail" :isRoomOrBox="isRoomOrBox" :roomNumber="room_number" @showAl="showAl"></bull-detail>
        <anbao-detail v-if="gameDetail==='anbao'" :enable="enable" :anbaoInfo="anbaoInfo" @cancelDetail="cancelDetail" :isRoomOrBox="isRoomOrBox" :roomNumber="room_number" @showAl="showAl"></anbao-detail>
        <div class="setting-mask" v-show="show" @click="hiddenMask" ref="mask">
            <div class="setting-area">
                <div class="setting">
                    <div class="setting-header">
                        <img :src="baseUrl+'storetitle.png'" alt="设置" class="setting-icon" @click="showSetting"></img>
                        <span class="setting-text">设置</span>
                    </div>
                    <div class="setting-tips">
                        <div class="tips-before"></div>
                        <div class="tips-text">组局区可显示好友创建的房间和包厢</div>
                        <div class="tips-after"> </div>
                    </div>
                    <div class="setting-control">
                        <img :src="baseUrl+'settingback.png'" alt="背景图片" class="setting-backimg">
                        <div class="setting-show" ref="setting-show">
                            <div class="setting-item" v-for="(item, index) in friends">
                                <span>{{item.nickname}}</span>
                                <img :src="baseUrl+'off.png'" alt="关闭" v-if="item.attention==='0'" @click="toOn(item.attention,item.manager_id, index)">
                                <img :src="baseUrl+'on.png'" alt="开启" v-if="item.attention==='1'" @click="toOff(item.attention, item.manager_id, index)">
                            </div>
                            <div class="setting-item more-data" v-show="more">
                                加载中...
                            </div>
                        </div>
                    </div>  
                </div>
            </div>
        </div>
        </div>    
    </div>    
    `,
    data: function(){
        return {
            baseUrl: baseUrl+'files/images/box/',
            show: false,
            linkStyle: {
                animation: ''
            },
            haveData:0,
            current: true,
            friends: [
            ],
            pageIndex:1,
            maxFriends: 0,
            more: false,
            // 查看每个游戏得包厢区域得数据结构
            games: [
                {
                    name:'飘三叶',
                    current:true,
                    index: 0,
                    sort: 'flower'
                },
                {
                    name:'斗牌',
                    current:false,
                    index: 1,
                    sort: 'bull'
                },
                {
                    name:'三公',
                    current:false,
                    index: 2,
                    sort: 'sangong'
                },
                {
                    name:'暗宝',
                    current:false,
                    index: 3,
                    sort: 'anbao'
                }
            ],
            // 选择游戏得数据结构
            gameImgs: [
                {
                    url: baseUrl+'files/images/box/'+'flower.png',
                    sort: 'flower'
                },
                {
                    url: baseUrl+'files/images/box/'+'bull.png',
                    sort: 'bull'
                },
                {
                    url:baseUrl+'files/images/box/'+'sg.png',
                    sort: 'sg'
                },
                {
                    url:baseUrl+'files/images/box/'+'dps.png',
                    sort: 'anbao'
                },
            ],
            listGames: {
                game: []
            },
            zoneShow: true,
            createRoom: false,
            selectGame: false,
            gameProperty: {
                flower: false,
                sg: false,
                bull: false,
                anbao: false,
                game: ''
            },
            // 三公创建包厢后得提示消息
            anbaoMessage: '',
            sgMessage: '',
            flowerMessage: '',
            bullMessage: '',
            anbaoResult: false,
            sgResult: false,
            bullResult: false,
            flowerResult: false,
            anbaoBoxStruct: [],
            sgBoxStruct: [],
            flowerBoxStruct: [],
            bullBoxStruct: [],
            commonBoxStruct: [],
            anbaoRoom: [],
            flowerRoom: [],
            bullRoom: [],
            sgRoom: [],
            commonRoom: [],
            gameDetail: '',
            enable: false,
            sgInfo: null,
            flowerInfo: null,
            bullInfo: null,
            anbaoInfo: null,
            isRoomOrBox: 0,
            room_number: '',
            messageTime: 2000
        }
    },
    props: ['name'],
    methods: {
        showSetting() {
            var _this = this;
            _this.$http.get('/manage/getManagerUser?page='+this.pageIndex).then(function(returnValue){
                console.log(111111111111111111)
                _this.pageIndex++;
                var result = returnValue.body;
                _this.friends = result.data;
                _this.maxFriends = Number(result.sum_count);
                _this.show = true;
                //监听好友列表滚动
                _this.$refs['setting-show'].addEventListener('scroll', this.listenSettingScroll,false);
            }).catch(function(){

            });
        },
        listenSettingScroll(){
            var _this = this;
                var scrollT, offsetH, scrollH;
                // 滚动的实时高度
                scrollT = _this.$refs['setting-show'].scrollTop;
                //获取元素的高度，不包括滚动
                offsetH = _this.$refs['setting-show'].offsetHeight;
                // 加上滚动条的高度
                scrollH = _this.$refs['setting-show'].scrollHeight;
                console.log(scrollT, offsetH, scrollH );

                if((scrollT+offsetH)>=scrollH && _this.haveData === 0){
                    _this.more = true;
                    _this.haveData = 1;
                    _this.$http.get('/manage/getManagerUser?page='+_this.pageIndex).then(check).catch(function(){

                    })
                }
                function check(data){
                    var j = 1;
                    console.log(j++)
                    _this.more = false;
                    _this.pageIndex++;
                    var result = data.body;
                    result.data.map(function(value, index){
                        // 防止加载到底部数据还没返回，然后重复加载数据
                        console.log(_this.friends);
                        _this.friends.push(value);
                    })
                    if(_this.friends.length === _this.maxFriends){
                        _this.haveData = 2;
                        this.$refs['setting-show'].removeEventListener('scroll', this.listenSettingScroll, false)
                    }else{
                        _this.scrollState = 0;
                    }
                }
        },
        hiddenMask(e) {
            if(e.target === this.$refs.mask){
                // 去掉监听器函数
                this.$refs['setting-show'].removeEventListener('scroll', this.listenSettingScroll, false)
                this.pageIndex = 1;
                this.friends = [];
                this.maxFriends = 0;
                this.haveData = 0;
                this.show = false;
                // 每次进去滑动条都在顶部
                this.$refs['setting-show'].scrollTop = 0 + 'px';
            }
        },
        //因为需求更改刷新界面变化
        // freshen() {
        //     var link = document.getElementsByClassName('freshen-link')[0];
        //     let _this = this;
        //     this.linkStyle.animation = 'linkChange .5s';
        //     link.addEventListener('animationend', function(){
        //         _this.linkStyle.animation = '';
        //     })
        // },
        toOn(attention, id, index) {
            if(attention === '0'){
                this.friends[index].attention = '1';
            }else{
                this.friends[index].attention = '0';
            }
            var postData = {
                attention: this.friends[index].attention,
                manager_id: id
            }
            this.$http.post('/manage/setManagerUser', JSON.stringify(postData))
                      .then(function(data){
                          console.log(data);
                      })
                      .catch(function(){

                      })
        },
        toOff(attention, id, index) {
            if(attention === '0'){
                this.friends[index].attention = '1';
            }else{
                this.friends[index].attention = '0';
            }
            var postData = {
                attention: this.friends[index].attention,
                manager_id: id
            }
            this.$http.post('/manage/setManagerUser', JSON.stringify(postData))
                      .then(function(data){
                          console.log(data);
                      })
                      .catch(function(){

                      })
        },
        quickJoin(box_number,game_type){
            var _this = this;
                var url = returnGameWsUrl(game_type);
                var wsObj = {
                    operation: 'JoinBox',
                    account_id: accountId,
                    session: session,
                    data: {
                        box_number: box_number,
                        game_type: game_type,
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
                    if(Number(obj.result) === -1){
                        _this.showAlertMessage(obj.result_message);
                        return;
                    }
                    else if(Number(obj.result) === 1){
                        _this.showAlertMessage(obj.result_message);
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
                        quickJoin(box_number, game_type);
                    }else {
                        return 0;
                    }
                }
                ws.onerror = function(err){
                    console.log('出错了');
                }
        },
        showAl(msg){
            this.gameDetail = '';
            this.showAlertMessage(msg);
        },
        gotoDetail() {
            var _this = this;
            this.$http.post('/box/getBoxList',JSON.stringify({account_id: accountId})).then(function(data){
		console.log("liuyong--");
		console.log(data);
                var result = JSON.parse(JSON.stringify(data.body)).data;
                var len = result.length-1;
		console.log("resul---");
                console.log(result);
		console.log("len:"+len);
                _this.$emit('recieveData',len, result);
            })
        },
        toCurrent(num) {
            var _this = this;
            this.games.map(function(value,index){
                if(num === index){
                    var sort = _this.games[index].sort;
                    _this.getBoxRoom(sort).then(function(data){
                        var result = JSON.parse(JSON.stringify(data)).body;
                        console.log(result)
                        if(sort === 'flower'){
                            _this.flowerBoxRoom(result);
                        }else if(sort === 'bull'){
                            _this.bullBoxRoom(result);
                        }else if(sort === 'sangong') {
                            _this.sgBoxRoom(result);
                        }else if(sort === 'anbao') {
                            _this.anbaoBoxRoom(result);
                        }
                    }).catch(function(){
                        console.log('出错');
                    });
                    value.current = true;
                }else {
                    value.current = false;
                }
            });

        },
        freashRoom(){
            var _this = this;
            this.games.map(function(value, index){
                if(value.current){
                    var sort = _this.games[index].sort;
                    _this.getBoxRoom(sort).then(function(data){
                        var result = JSON.parse(JSON.stringify(data)).body;
                        console.log(result)
                        if(sort === 'flower'){
                            _this.flowerBoxRoom(result);
                        }else if(sort === 'bull'){
                            _this.bullBoxRoom(result);
                        }else if(sort === 'sangong') {
                            _this.sgBoxRoom(result);
                        }else if(sort === 'anbao') {
                            _this.anbaoBoxRoom(result);
                        }
                    }).catch(function(){
                        console.log('出错');
                    });
                }
            })
        },
        // 选择每个分类游戏后得具体游戏的数据结构
        gameList(sort) {
            this.createRoom = false;
            this.selectGame = true;
            if(sort === 'flower'){
                this.listGames.sort = [
                    {
                        sort: 'flower',
                        url: this.baseUrl + 'f.png',
                        title: 'flower'
                    },
                    {
                        sort: 'flower',
                        url: this.baseUrl + 'tf.png',
                        title: 'tflower'
                    },
                    {
                        sort: 'flower',
                        url: this.baseUrl + 'bf.png',
                        title: 'bflower'
                    }
                ]
            }else if(sort === 'bull'){
                this.listGames.sort = [
                    {
                        sort: 'bull',
                        url: this.baseUrl +'b.png',
                        title: 'bull'
                    },
                    {
                        sort: 'bull',
                        url: this.baseUrl +'nb.png',
                        title: 'nbull'
                    },
                    {
                        sort: 'bull',
                        url: this.baseUrl +'tb.png',
                        title: 'tbull'
                    },
                    {
                        sort: 'bull',
                        url: this.baseUrl +'twb.png',
                        title: 'twbull'
                    },
                    {
                        sort: 'bull',
                        url: this.baseUrl +'thb.png',
                        title: 'thbull'
                    }
                ]
            }else if(sort === 'sg'){
                this.listGames.sort = [
                    {
                        sort:'sg',
                        url: this.baseUrl +'s.png',
                        title: 'sg'
                    },
                    {
                        sort: 'sg',
                        url:this.baseUrl + 'ns.png',
                        title: 'nsg'
                    }
                ]
            }else if(sort === 'anbao') {
                this.listGames.sort = [
                    {
                        sort: 'anbao',
                        url: this.baseUrl + 'list-dp.png',
                        title: 'anbao'
                    },
                ]
            }
        },
        toCreateRoom() {
            this.createRoom = true;
        },
        closeCreateRoom() {
            this.createRoom = false;
        },
        closeSelectGame() {
            this.selectGame = false;
        },
        showGameProperty(sort, title) {
            this.selectGame = false;
            this.gameProperty.game = title;
            this.gameProperty[sort] = true;
        },
        cancelFlowerP(){
            this.gameProperty.flower = false;
        },
        cancelBullP(){
            this.gameProperty.bull = false;
        },
        cancelSgP(){
            this.gameProperty.sg = false;
        },
        cancelAnbaoP(){
            this.gameProperty.anbao = false;
        },
        sgReturn(message){
            var _this = this;
            this.gameProperty.sg = false;
            this.sgResult = true;
            this.sgMessage = message;
            setTimeout(function(){
                _this.sgResult = false;
            }, _this.messageTime);
            this.getBoxRoom('sangong').then(function(returnValue){
                _this.sgBoxRoom(JSON.parse(JSON.stringify(returnValue)).body);
            }).catch(function(){
                console.log('出错了');
            });
        },
        flowerReturn(message){
            var _this = this;
            this.gameProperty.flower = false;
            this.flowerResult = true;
            this.flowerMessage = message;
            setTimeout(function(){
                _this.flowerResult = false;
            }, _this.messageTime);
            this.getBoxRoom('flower').then(function(returnValue){
                _this.flowerBoxRoom(JSON.parse(JSON.stringify(returnValue)).body);
            }).catch(function(){
                console.log('出错了');
            });
        },
        bullReturn(message){
            console.log(message);
            var _this = this;
            this.gameProperty.bull = false;
            this.bullResult = true;
            this.bullMessage = message;
            setTimeout(function(){
                _this.bullResult = false;
            }, _this.messageTime);
            this.getBoxRoom('bull').then(function(returnValue){
                _this.bullBoxRoom(JSON.parse(JSON.stringify(returnValue)).body);
            }).catch(function(){
                console.log('出错了');
            });
        },
        anbaoReturn(message){
            console.log(message);
            var _this = this;
            this.gameProperty.anbao = false;
            this.anbaoResult = true;
            this.anbaoMessage = message;
            setTimeout(function(){
                _this.anbaoResult = false;
            }, _this.messageTime);
            this.getBoxRoom('anbao').then(function(returnValue){
		var vatre = JSON.parse(JSON.stringify(returnValue));
		console.log(vatre);
                _this.anbaoBoxRoom(JSON.parse(JSON.stringify(returnValue)).body);
            }).catch(function(){
                console.log('出错了');
            });
        },
        // 获取组局区的好友和包厢
        getBoxRoom(game){
            // 直接返回一个promise,实现细节交给具体的函数
            var obj = this.$http.get('/manage/getRoomList?game_category='+game);
		console.log(obj);
		console.log('/manage/gewtRoomList?game_category='+game);
		return obj;
        },
        //处理三公获取包厢后的数据
        sgBoxRoom(data){
            var _this = this;
            var result = data.data;
            console.log(result);
            this.sgBoxStruct = [];
            this.sgRoom = [];
            this.games.map(function(item){
                item.current = false;
            })
            this.games[2].current = true;
            result.map(function(value){
                // info_type：1包厢
                if(value.info_type===1){
                    _this.sgBoxStruct.push(value);
                }else {
                    _this.sgRoom.push(value);
                }
            });
            this.commonBoxStruct = this.sgBoxStruct;
            this.commonRoom = this.sgRoom;
            this.$refs.zoneWrap.style.width = (this.commonBoxStruct.length+this.commonRoom.length)*27 + 'vw';
        },
        //处理扎金花获取包厢后返回的数据
        flowerBoxRoom(data) {
            var _this = this;
            var result = data.data;
            console.log(result);
            this.flowerBoxStruct = [];
            this.flowerRoom = [];
            this.games.map(function(item){
                item.current = false;
            })
            this.games[0].current = true;
            result.map(function(value){
                // info_type：1包厢
                if(value.info_type===1){
                    _this.flowerBoxStruct.push(value);
                }else{
                    _this.flowerRoom.push(value);
                }
            });
            this.commonBoxStruct = this.flowerBoxStruct;
            this.commonRoom = this.flowerRoom;
            console.log(this.commonBoxStruct);
            this.$refs.zoneWrap.style.width = (this.commonBoxStruct.length+this.commonRoom.length)*27 + 'vw';
        },
        // 处理牛牛获取爆香后返回的数据
        bullBoxRoom(data) {
            var _this = this;
            var result = data.data;
            this.bullBoxStruct = [];
            this.bullRoom = [];
            this.games.map(function(item){
                item.current = false;
            })
            this.games[1].current = true;
            result.map(function(value){
                // info_type：1包厢
                if(value.info_type===1){
                    _this.bullBoxStruct.push(value);
                }else{
                    _this.bullRoom.push(value);
                }
            });
            this.commonBoxStruct = this.bullBoxStruct;
            this.commonRoom = this.bullRoom;
            this.$refs.zoneWrap.style.width = (this.commonBoxStruct.length+this.commonRoom.length)*27 + 'vw';
        },
        anbaoBoxRoom(data) {
            var _this = this;
            var result = data.data;
            this.anbaoBoxStruct = [];
            this.anbaoRoom = [];
            this.games.map(function(item){
                item.current = false;
            })
            this.games[3].current = true;
            result.map(function(value){
                // info_type：1包厢
                if(value.info_type===1){
                    _this.anbaoBoxStruct.push(value);
                }else{
                    _this.anbaoRoom.push(value);
                }
            });
            this.commonBoxStruct = this.anbaoBoxStruct;
            this.commonRoom = this.anbaoRoom;
            this.$refs.zoneWrap.style.width = (this.commonBoxStruct.length+this.commonRoom.length)*27 + 'vw';
        },
        cancelDetail(){
            this.gameDetail = '';
        },
        showAlertMessage(message){
            var _this = this;
            this.flowerMessage = message;
            this.flowerResult = true;
            setTimeout(function(){
                _this.flowerResult = false;
            }, _this.messageTime);
        }
        ,
        showDetail(id, number, personId){
            var _this = this;
            this.isRoomOrBox = 0;
            var postData = {
                account_id: personId,
                box_id: id,
                box_number: number
            }
            this.$http.post('/box/getBoxInfo', postData).then(function(returnValue){
                var result = returnValue.body;
                if(result.result===0){
                    _this.games.map(function(value, index) {
                        if(value.current){
                            if(index===0){
                                _this.flowerInfo = result;
                                _this.gameDetail = 'flower';
                            }else if(index===1){
                                _this.bullInfo = result;
                                _this.gameDetail = 'bull';
                            }else if(index===2){
                                _this.sgInfo = result;
                                _this.gameDetail = 'sg';
                            }else if(index===3){
                                _this.anbaoInfo = result;
                                _this.gameDetail = 'anbao';
                            }
                            console.log(_this.gameDetail);
                        }
                    });
                }else if(result.result===-1){
                _this.showAlertMessage(result.result_message);
                }

            }).catch(function(error){
                console.log('出错了');
            })
        },
        showRoomDetail(id, type, number){
            var _this = this;
            this.room_number = number;
            this.isRoomOrBox = 1;
            this.$http.get('/manage/GetRoomInfo?room_id='+id+'&game_type='+type).then(function(returnValue){
                var result = returnValue.body;
                if(result.result===0){
                    _this.games.map(function(value, index) {
                        if(value.current){
                            if(index===0){
                                _this.flowerInfo = result;
                                _this.gameDetail = 'flower';
                            }else if(index===1){
                                _this.bullInfo = result;
                                _this.gameDetail = 'bull';
                            }else if(index===2){
                                _this.sgInfo = result;
                                _this.gameDetail = 'sg';
                            }else if(index===3){
                                _this.anbaoInfo = result;
                                _this.gameDetail = 'anbao';
                            }
                            console.log(_this.gameDetail);
                        }
                    });
                }
            }).catch(function(error){
                console.log('出错了');
            })
        }
    },
    components: {
        'flowerP': flowerP,
        'bullP': bullP,
        'sgP': sgP,
        'anbaoP': anbaoP,
        'sgDetail': sgDetail,
        'flowerDetail': flowerDetail,
        'bullDetail': bullDetail,
        'anbaoDetail': anbaoDetail
    },
    beforeMount: function(){
        console.log(flowerDetail)
        var _this = this;
        //页面装载后开始发起获取包厢和房间的请求
        this.$nextTick(function(){
            this.getBoxRoom('flower').then(function(data){
                _this.flowerBoxRoom(JSON.parse(JSON.stringify(data)).body);
            });
        });
    }
}
