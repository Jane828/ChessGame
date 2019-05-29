#!/usr/bin/env bash
case $1 in
    f6) # 炸金花 大牌炸金花
        (cd timer && php ./T20039/start.php start) &
        (cd socket && php ./Flower20032/start.php start)
        ;;
    f10) # 十人炸金花
        (cd timer && php ./T20089/start.php start) &
        (cd socket && php ./TFlower20082/start.php start)
        ;;
    fb) # 十三人斗牛
        (cd timer && php ./T30039/start.php start) &
        (cd socket && php ./FBull20062/start.php start)
        ;;
    tb) # 十二人斗牛
        (cd timer && php ./T20069/start.php start) &
        (cd socket && php ./TBull20062/start.php start)
        ;;
    nb) # 九人斗牛
        (cd timer && php ./T20029/start.php start) &
        (cd socket && php ./NBull20022/start.php start)
        ;;
    bu) # 六人斗牛
        (cd timer && php ./T20019/start.php start) &
        sleep 1
        (cd socket && php ./Bull20012/start.php start)
        ;;
    lb) # 十人癞子牛
        (cd timer && php ./T20099/start.php start) &
        (cd socket && php ./LBull20092/start.php start)
        ;;
    sg6) # 六人三公
        (cd timer && php ./T20069/start.php start) &
        (cd socket && php ./Sg30062/start.php start)
        ;;
    sg9) # 九人三公
        (cd timer && php ./T20029/start.php start) &
        (cd socket && exec php ./Sg30022/start.php start)
        ;;
    vf6) # vip炸金花
        (cd timer && php ./T20059/start.php start) &
        (cd socket && php ./VFlower20052/start.php start)
        ;;
    vbu) # vip六人斗牛
        (cd timer && php ./T50019/start.php start) &
        (cd socket && php ./VBull50012/start.php start)
        ;;
    vnb) # vip九人斗牛
        (cd timer && php ./T20049/start.php start) &
        (cd socket && php ./VBull20042/start.php start)
        ;;
    vtb) # vip十二人斗牛
        (cd timer && php ./T50029/start.php start) &
        (cd socket && php ./VBull50022/start.php start)
        ;;
    front)
        (cd socket && php ./FrontWSocket/start.php start)
        ;;
    *)
        echo "invalid $*"
        ;;
esac

echo "exit"
