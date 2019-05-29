var bottomTab = {
    name: 'bottomTab',
    template: `
    <div class="bottom-menu">
        <ul>
            <li class="menu-item" v-for="item in tabs">
                <a :href="item.linkUrl">
                    <img :src="item.imageUrl" />
                    <p>{{item.tab}}</p>
                    <span></span>
                </a>
            </li>
        </ul>
    </div>
    `,
    data: function() {
        return {
            tabs: [
                {
                    linkUrl: baseUrl+'f/'+'ym',
                    imageUrl: imageUrl + 'files/images/hall/'+'game.png',
                    tab:'游戏'
                },
                {
                    linkUrl: baseUrl+'f/'+'fri',
                    imageUrl: imageUrl + 'files/images/hall/'+'friend.png',
                    tab:'好友'
                },
                {
                    linkUrl: baseUrl+'f/'+'box',
                    imageUrl: imageUrl + 'files/images/hall/'+'box.png',
                    tab:'包厢'
                },
                {
                    linkUrl: baseUrl+'f/'+'yh',
                    imageUrl: imageUrl + 'files/images/hall/'+'user.png',
                    tab:'个人'
                },
            ]
        }
    },
    methods: {
        
    },
    mounted: function(){
        var _this = this;
        this.$nextTick(function(){
            var threeItem = document.getElementsByClassName('menu-item')[2];
            threeItem.className += ' menu-item-selected';
        })
    }
}