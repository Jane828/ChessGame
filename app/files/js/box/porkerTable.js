var porkerTable = {
    name: 'porkerTable',
    props:['table'],
    template: `
        <div class="porkertable-wrap">
            <div v-if="table.length===0" class="noTable">
                暂无牌桌
            </div>
            <div class="porkertable-index" v-for="item in table" v-if="table.length!==0">
                <img :src="baseUrl+'porkertable.png'" alt="牌桌" v-if="Number(item.is_close)===0">
                <img :src="baseUrl+'endPorkerTable.png'" alt="牌桌" v-if="Number(item.is_close)===1">
                <div class="porkertable-progress">
                    <ul>
                        <li>
                            <img :src="baseUrl+'porkerNum.png'" alt="局数">
                            <span>{{item.game_num}}/{{item.total_num}}</span>
                        </li>
                        <li>
                            <img :src="baseUrl+'people.png'" alt="人数">
                            <span>{{item.player_num}}/{{item.player_max_num}}</span>
                        </li>
                    </ul>
                </div>
                <div class="porkertable-do" @click=" Number(item.is_close)===0 ? joinGame(item.game_type, item.room_number) : ''">快速加入</div>
                <div class="porkertable-time">
                    <p>房间号:{{item.room_number}}</p>
                    <p>{{new Date(item.create_time*1000).getFullYear()+' '+(new Date(item.create_time*1000).getMonth()+1)+'.'+new Date(item.create_time*1000).getDate()+' '+new Date(item.create_time*1000).getHours()+':'+new Date(item.create_time*1000).getMinutes()}}</p>
                </div>
            </div>
        <div>
    `,
    data: function() {
        return {
            baseUrl: baseUrl+'files/images/box/',
            table: this.table
        }
    },
    methods: {
        joinGame(game_type, roomNumber){
            if(Number(game_type) === 5){
                window.location.href = baseUrl + 'f/b?i=' + roomNumber + '_';
            }else if(Number(game_type) === 9){
                window.location.href = baseUrl + 'f/nb?i=' + roomNumber + '_';
            }else if(Number(game_type) === 71){
                window.location.href = baseUrl + 'f/lb?i=' + roomNumber + '_';
            }else if(Number(game_type) === 12){
                window.location.href = baseUrl + 'f/tb?i=' + roomNumber + '_';
            }else if(Number(game_type) === 13){
                window.location.href = baseUrl + 'f/fb?i=' + roomNumber + '_';                   
            }else if(Number(game_type) === 36){
                window.location.href = baseUrl + 'f/sg?i=' + roomNumber + '_';
            }else if(Number(game_type) === 37){
                window.location.href = baseUrl + 'f/nsg?i=' + roomNumber + '_';
            }else if(Number(game_type) === 1){
                window.location.href = baseUrl + 'f/yf?i=' + roomNumber + '_';            
            }else if(Number(game_type) === 110){
                window.location.href = baseUrl + 'f/tf?i=' + roomNumber + '_';
            }else if(Number(game_type) === 111){
                window.location.href = baseUrl + 'f/bf?i=' + roomNumber + '_';
            }
        }
    },

}