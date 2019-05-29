var porkerTable = {
    name: 'porkerTable',
    template: `
        <div class="porkertable-wrap">
            <div class="porkertable-index">
                <img :src="baseUrl+'porkertable.png'" alt="牌桌">
                <div class="porkertable-progress">
                    <ul>
                        <li>
                            <img :src="baseUrl+'porkerNum.png'" alt="局数">
                            <span>11/12</span>
                        </li>
                        <li>
                            <img :src="baseUrl+'people.png'" alt="人数">
                            <span>11/14</span>
                        </li>
                    </ul>
                </div>
                <div class="porkertable-do">快速加入</div>
                <div class="porkertable-time">
                    <p>房间号:11111</p>
                    <p>2018 6.22 15:30</p>
                </div>
            </div>
        <div>
    `,
    data: function() {
        return {
            baseUrl: baseUrl+'files/images/box/',
        }
    },
    methods: {

    },

}