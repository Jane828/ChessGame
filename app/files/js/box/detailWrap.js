var detailWrap = {
    name: 'detailWrap',
    props: ['boxLen', 'result'],
    template: `
        <div class="detailWrap-wrap" >
            <box-detail v-if="show" @showTable="showTable" :boxLen="boxLen" :result="result"></box-detail>
            <porker-table v-if="!show" :table="table"></porker-table>
        </div>
    `,
    data: function(){
        return {
            boxLen: this.boxLen,
            result: this.result,
            show: true,
            table: []
        }
    },
    methods:{
        showTable(table) {
            this.table = table;
            this.show = false;
        }
    },
    components: {
        'boxDetail': boxDetail,
        'porkerTable': porkerTable
    },
    mounted: function(){
        console.log("detail组件已挂载");
    }
}
