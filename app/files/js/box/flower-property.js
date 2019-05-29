var flowerProperty = {
    //分数
    scores:[
        2, 4, 10, 20, 40, 100, 200, 0
    ],
    //注数
    chips: [
        '2/4', '4/8', '5/10','8/16', '10/20', '20/40', '40/80', '50/100', '100/200'
    ],
    scoreList:function(){
        var arr = [];
        for(var i = 1; i <= 200; i++){
            arr.push(i);
        }
        return arr;
    },
    //时间准备和下注
    date: {
        ready: [5,6,7,8,9,10],
        toChips: [5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20]
    },
    //看牌上限
    seenLimit: 2000,
    //上限
    upLimit: 2000,
    //比牌上限
    compareLimit: 2000,
    //局数
    boardNum: [
        '10局x1房卡', '20局x2房卡'
    ],
    tenBoardNum: [
        '10局x2房卡', '20局x4房卡'
    ],
    //喜牌
    happyCards: [
        0, 5, 10, 20, 40
    ],
    special: '235吃豹子',
    firstCompare: '首轮禁止比牌',
}