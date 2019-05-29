var boxWrap = {
    name: 'boxWrap',
    template: `
        <div class="father-wrap" style="height:90vh; overflow:hidden;">
            <box-main v-if="detailHidden"  @recieveData="recieveData"></box-main>
            <detail-wrap v-if="!detailHidden" :boxLen="boxLen" :result="result"></detail-wrap>
        </div>
    `,
    data: function() {
        return {
            detailHidden: true,
            boxLen: 0,
            result: null
        }
    },
    methods: {
        recieveData(boxLen, result) {
            this.boxLen = boxLen;
            this.result = result;
            this.detailHidden = false;
        }
    },
    components: {
        'boxMain': boxMain,
        'detailWrap': detailWrap
    },
}