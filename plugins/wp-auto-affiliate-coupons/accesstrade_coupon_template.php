<style>
/*
* Coupon area
*/
.coupondiv {
    border: 1px solid #d3d3d3;
    min-width: 250px;
    margin-bottom: 6px;
    background-color: #fff
}
.coupondiv .promotiontype {
    padding: 15px;
    overflow: hidden
}
.promotag {
    float: left
}
.promotagcont {
    background: #fff;
    color: #008ddb;
    overflow: hidden;
    width: 100px;
    border-radius: 2px;
    -webkit-box-shadow: 1px 1px 4px rgba(34, 34, 34, .2);
    box-shadow: 1px 1px 4px rgba(34, 34, 34, .2);
    text-align: center
}
.promotagcont .saleorcoupon {
    background: #008ddb;
    padding: 7px 6px;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    line-height: 2em
}
.tagsale.promotagcont {
    background: #fff;
    color: #1fb207
}
.tagsale .saleorcoupon {
    background: #1fb207
}
.saveamount {
    min-height: 58px;
    font-size: 20px;
    margin: 0 auto;
    padding: 4px 3px 0;
    font-weight: 700;
    line-height: 2.5
}
.coupondiv .cpbutton {
    float: right;
    position: relative;
    z-index: 1;
    text-align: right;
    margin-top: 35px;
    margin-right: 15px
}
.copyma {
    width: 110px;
    min-width: 110px;
    display: inline-block;
    position: relative;
    margin-right: 30px;
    padding: 15px 5px;
    border: 0;
    background: #008ddb;
    color: #fff;
    font-family: 'Roboto', sans-serif;
    font-size: 15px;
    font-weight: 500;
    line-height: 1;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
    border-style: solid;
    border-color: #008ddb;
    border-radius: 0
}
.copyma:after {
    border-left-color: #008ddb;
    content: "";
    display: block;
    width: 0;
    height: 0;
    border-top: 45px solid transparent;
    border-left: 45px solid #008ddb;
    position: absolute;
    right: -45px;
    top: 0
}
/* .copyma:hover {
    background-color: #cb5912
} */
.copyma:hover:after {
    opacity: 0;
    -webkit-transition-duration: .5s;
    transition-duration: .5s
}
.coupon-code {
    position: absolute;
    top: 0;
    right: -45px;
    z-index: -1;
    min-width: 50px;
    height: 45px;
    padding-right: 5px;
    overflow: hidden;
    font-weight: 500;
    line-height: 45px;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
    border-radius: 0;
    font-size: 16px;
    color: #222;
    font-family: 'Open Sans', sans-serif;
    border: 1px solid #ddd
}
.xemngayz {
    display: inline-block;
    position: relative;
    padding: 15px 15px;
    border: 0;
    background-color: #378f00;
    border: 1px solid #2c7200;
    color: #fff;
    font-family: 'Roboto', sans-serif;
    font-size: 16px;
    font-weight: 500;
    line-height: 1;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
    border-style: solid;
    border-radius: 0
}
.xemngayz:hover {
    background-color: #2c7200
}
.promotiondetails {
    padding-left: 20px;
    width: calc(100% - 270px);
    word-wrap: break-word;
    float: left;
    font-size: 16px
}
.coupontitle {
    display: block;
    font-family: 'Roboto', sans-serif;
    margin-bottom: 5px;
    color: #222;
    font-weight: 500;
    line-height: 1.2;
    text-decoration: none;
    font-size: 16px
}
.cpinfo {
    display: block;
    margin-bottom: 5px;
    color: #222;
    line-height: 1.6;
    text-decoration: none;
    font-size: 14px
}
.news-box .news-thumb,
.news-box .news-info {
    display: inline-block;
    float: left
}
.news-box .news-info {
    width: 500px;
    margin-left: 10px
}
.coupon-text {
    font-weight: 600;
}

@media screen and (max-width: 767px) {
    .coupontitle {
        font-size: 18px
    }
    .promotagcont {
        width: 60px
    }
    .promotagcont .saleorcoupon {
        font-size: 11px
    }
    .saveamount {
        min-height: 50px;
        font-size: 16px
    }
    .promotiondetails {
        margin-right: 0;
        font-size: 14px;
        width: auto;
        float: none;
        margin-left: 70px;
        padding-left: 0
    }
    .coupondiv .cpbutton {
        clear: both;
        margin-top: 0;
        /* margin-right: 35px !important; */
        /* width: 165px */
    }
    .copyma {
        width: 100px;
        min-width: 100px;
        padding: 10px 8px
    }
    .copyma:after {
        border-top: 35px solid transparent;
        border-left: 35px solid #008ddb;
        position: absolute;
        right: -34px;
        top: 0
    }
    .coupon-code {
        position: absolute;
        top: 0;
        right: -35px;
        z-index: -1;
        height: 35px;
        line-height: 35px
    }
    .xemngayz {
        width: 150px;
        min-width: 135px;
        padding: 10px 8px
    }
    .cpbutton-deal {
        margin-right: 10px !important;
    }
    /* .xemngayz:hover {
        background-color: #167f05
    } */
}
</style>
<script type="text/javascript">
function nhymxu_at_coupon_copy2clipboard( coupon_value ) {
    var aux = document.createElement("input");
    aux.setAttribute("value", coupon_value);
    document.body.appendChild(aux);

    if( navigator.userAgent.match(/ipad|ipod|iphone/i) ) {
		var editable = aux.contentEditable;
		var readOnly = aux.readOnly;

		aux.contentEditable = true;
		aux.readOnly = false;

		var range = document.createRange();
		range.selectNodeContents(aux);

		var selection = window.getSelection();
		selection.removeAllRanges();
		selection.addRange(range);

		aux.setSelectionRange(0, 999999);
		aux.contentEditable = editable;
		aux.readOnly = readOnly;

    } else {
        aux.select();
    }
    document.execCommand("copy");
    document.body.removeChild(aux);
}

</script>
<?php foreach( $at_coupons as $row ): ?>
    <div class="coupondiv">
        <div class="promotiontype">
            <div class="promotag">
                <div class="promotagcont tagsale">
                    <div class="saveamount"><?=($row['thumb'] != '') ? $row['thumb'] : '';?></div>
                    <div class="saleorcoupon"><?=($row['code']) ? ' COUPON' : ' DEAL';?></div>
                </div>
            </div>
            <div class="promotiondetails">
                <div class="coupontitle"><?=$row['title'];?></div>
                <div class="cpinfo">
                    <strong>Hạn dùng: </strong><?=date('d-m-Y', strtotime($row['exp']));?>
                    <?php if( !empty($row['categories']) ): ?>
                    <br><strong>Ngành hàng:</strong> <?=implode(',', $row['categories']);?>
                    <?php endif; ?>
                    <?=( $row['note'] != '' ) ? '<br>' . $row['note'] : '';?>
                </div>
            </div>
            <?php if( $row['code'] != '' ): ?>
            <div class="cpbutton">
                <div class="copyma" onclick="window.open('#?code=<?=$row['code'];?>&url=<?=$row['url'];?>','_blank');window.open('<?=$row['deeplink'];?>','_self');">
                    <div class="coupon-code"><?=$row['code'];?></div>
                    <div class="coupon-text">LẤY MÃ</div>
                </div>
            </div>
            <?php else: ?>
            <div class="cpbutton cpbutton-deal" style="margin-right:0;">
                <div class="xemngayz coupon-text" onclick="window.open('<?=$row['deeplink'];?>','_blank')">XEM KHUYẾN MẠI</div>
            </div>
            <?php endif; ?>
        </div>
    </div>
<?php
endforeach;
?>
<script>
    setTimeout(() => {
        if (location.hash.includes('#?code=')) {
            var code = getUrlParameter(location.hash, 'code');
            var url = getUrlParameter(location.hash, 'url');
            nhymxu_at_coupon_copy2clipboard(code);
            if (window.prompt("Nhấn OK để COPY mã giảm giá, sau đó bạn sẽ được chuyển đến trang mua hàng", code)) {
                // window.open(url, '_blank');
                setTimeout(() => {
                    window.close();
                }, 500);
            }
        }
    }, 1000);

    function getUrlParameter(url, name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(url);
        return results === null ? '#' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        };
</script>
<?php
