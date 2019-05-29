var app = new Vue({
    el:'#box',
    data: {
        message: 'hello world',
        changeToDetail: true
    },
    methods: {
        toDetail() {
            console.log(11111111112222222222222222)
        }  
    },
    components: {
        'boxWrap': boxWrap,
        'bottomTab': bottomTab,
    }
});
Vue.use(VueResource);