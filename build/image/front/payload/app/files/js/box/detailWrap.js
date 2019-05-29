var detailWrap = {
    name: 'detailWrap',
    props: ['boxLen', 'result'],
    template: `
        <div class="detailWrap-wrap" >
            <box-detail v-show="show" @showTable="showTable" :boxLen="boxLen" :result="result"></box-detail>
            <porker-table v-show="!show" ></porker-table>
        </div>
    `,
    data: function(){
        return {
            boxLen: this.boxLen,
            result: this.result,
            show: true,
        }
    },
    methods:{
        showTable() {
            this.show = false;
        }
    },
    components: {
        'boxDetail': boxDetail,
        'porkerTable': porkerTable
    },
    mounted: function(){
        console.log(1);
    }
}