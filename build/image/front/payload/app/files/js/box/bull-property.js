var bullProperty = {
    modes: [
        {
            up: '自由',
            down: '抢庄'
        },
        {
            up: '明牌',
            down: '抢庄'
        },
        {
            up: '牛牛',
            down: '上庄'
        },
        {
            up: '通比',
            down: '牛牛'
        },
        {
            up: '固定',
            down: '庄家'
        },
    ],
    // 底分
    scores: [ 1, 2, 3, 5,''],
    tongScores: [ 5, 10, 20,''],
    scoreList: function(){
        var arr = [];
        for(var i = 1; i <= 100; i++){
            arr.push(i);
        }
        return arr;
    },
    times: [
        {
            text: '准备',
            select: [5,6,7,8,9,10]
        },
        {
            text: '抢庄',
            select: [5,6,7,8,9,10]
        },
        {
            text: '下注',
            select: [5,6,7,8,9,10]
        },
        {
            text: '摊牌',
            select: [5,6,7,8,9,10]
        }
    ],
    rules: ['牛牛x3牛九x2牛八x2', '牛牛x4牛九x3牛八x2牛七x2'],
    porkers: ['四花牛(4倍)', '五花牛(5倍)', '顺子牛(6倍)', '同花牛(6倍)','葫芦牛(6倍)', '炸弹牛(6倍)', '同花顺(7倍)', '五小牛(8倍)'],
    nums: ['12局x2房卡', '24句x4房卡'],
    bullNums: ['10局x1房卡', '20局x2房卡'],
    multiples: ['1, 2, 4, 5','1, 3, 5, 8', '2, 4, 6, 10'],
    ups: ['无', 300, 500, 1000]
}