<html>
    <meta charset="utf-8">
    <body >
        <a href="/">戻る</a></br>
        <!-- Styles -->
        <style>
            #chartdiv {
                width: 100%;
                height: 500px;
            }
            
            .wrap-table {
                overflow-x: scroll;
            }
            
            .wrap-table table th {
                min-width: 70px;
            }
        </style>

        <!-- Resources -->
        <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
        <script src="https://www.amcharts.com/lib/3/serial.js"></script>
        <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
        <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
        <script src="https://www.amcharts.com/lib/3/themes/none.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/i18n/jquery.ui.datepicker-ja.min.js"></script>
        <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/redmond/jquery-ui.css" >
        <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css" >

        <!-- Chart code -->
        <script>
            $(function () {
                $("#date1").datepicker();
                $("#date2").datepicker();
            });

            function editText(obj, date) {
                var text = $(obj).val();

                $.getJSON("http://59.106.209.199:1111/edittext", {
                    text: text,
                    date: date
                },
                        function (json) {
                        }
                );
            }
        </script>

        <script>
            var chart = AmCharts.makeChart("chartdiv", {
                "type": "serial",
                "theme": "none",
                "legend": {
                    "equalWidths": false,
                    "useGraphSettings": true,
                    "valueAlign": "left",
                    "valueWidth": 120
                },
                "dataProvider": [
            {foreach from=$data item=aja name=pero}
                    {
                        "date": "{$aja.keydate}",
                        "install": {$aja.installCnt + installCnt2},
                        "iOS login": {$aja.loginCnt},
                        "AOS login": {$aja.loginCntAos},
                        "iOS list": {$aja.listCnt},
                        "AOS list": {$aja.listCntAos2}
                    },
            {/foreach}
                ],
                "valueAxes": [{
                        "id": "installAxis",
                        "axisAlpha": 0,
                        "gridAlpha": 0,
                        "position": "left",
                        "title": "インストール"
                    }, {
                        "id": "loginAxis",
                        "axisAlpha": 0,
                        "gridAlpha": 0,
                        "labelsEnabled": false,
                        "position": "right"
                    }, {
                        "id": "listAxis",
                        "axisAlpha": 0,
                        "gridAlpha": 0,
                        "inside": true,
                        "position": "right",
                        "title": "投稿数"
                    }, {
                        "id": "loginAxis1",
                        "axisAlpha": 0,
                        "gridAlpha": 0,
                        "labelsEnabled": false,
                        "position": "right"
                    }, {
                        "id": "listAxis1",
                        "axisAlpha": 0,
                        "gridAlpha": 0,
                        "inside": true,
                        "position": "right",
                        "title": "投稿数"
                    }],
                        "graphs": [{
                        "alphaField": "alpha",
                        "balloonText": "[[value]] インストール",
                        "dashLengthField": "dashLength",
                        "fillAlphas": 0.7,
                        "legendPeriodValueText": "合計: [[value.sum]] 人",
                        "legendValueText": "[[value]] 人",
                        "title": "インストール",
                        "type": "column",
                        "valueField": "install",
                        "valueAxis": "インストール"
                    }, {
                        "balloonText": "iOSDAU:[[value]]人",
                        "bullet": "round",
                        "bulletBorderAlpha": 1,
                        "useLineColorForBulletBorder": true,
                        "bulletColor": "#FFFFFF",
                        "dashLengthField": "dashLength",
                        "labelPosition": "right",
                        "labelText": "[[value]]",
                        "legendValueText": "[[value]]",
                        "title": "iOSDAU",
                        "fillAlphas": 0,
                        "valueField": "iOS login",
                        "valueAxis": "loginAxis"
                    }, {
                        "bullet": "square",
                        "bulletBorderAlpha": 1,
                        "bulletBorderThickness": 1,
                        "dashLengthField": "dashLength",
                        "balloonText": "iOS投稿数:[[value]]",
                        "legendPeriodValueText": "合計: [[value.sum]] 回",
                        "legendValueText": "iOS投稿数:[[value]]",
                        "title": "iOS投稿数",
                        "fillAlphas": 0,
                        "valueField": "iOS list",
                        "valueAxis": "listAxis"
                    }, {
                        "balloonText": "AOSDAU:[[value]]人",
                        "bullet": "round",
                        "bulletBorderAlpha": 1,
                        "useLineColorForBulletBorder": true,
                        "bulletColor": "#FFFFFF",
                        "dashLengthField": "dashLength",
                        "labelPosition": "right",
                        "labelText": "[[value]]",
                        "legendValueText": "[[value]]",
                        "title": "AOSDAU",
                        "fillAlphas": 0,
                        "valueField": "AOS login",
                        "valueAxis": "loginAxis1"
                    }, {
                        "bullet": "square",
                        "bulletBorderAlpha": 1,
                        "bulletBorderThickness": 1,
                        "dashLengthField": "dashLength",
                        "balloonText": "AOS投稿数:[[value]]",
                        "legendPeriodValueText": "合計: [[value.sum]] 回",
                        "legendValueText": "AOS投稿数:[[value]]",
                        "title": "AOS投稿数",
                        "fillAlphas": 0,
                        "valueField": "AOS list",
                        "valueAxis": "listAxis1"
                    }],
                "chartCursor": {
                    "categoryBalloonDateFormat": "DD",
                    "cursorAlpha": 0.1,
                    "cursorColor": "#000000",
                    "fullWidth": true,
                    "valueBalloonsEnabled": false,
                    "zoomable": false
                },
                "dataDateFormat": "YYYY-MM-DD",
                "categoryField": "date",
                "categoryAxis": {
                    "dateFormats": [{
                            "period": "DD",
                            "format": "DD"
                        }, {
                            "period": "WW",
                            "format": "MMM DD"
                        }, {
                            "period": "MM",
                            "format": "MMM"
                        }, {
                            "period": "YYYY",
                            "format": "YYYY"
                        }, {
                            "period": "MM",
                            "format": "MMM"
                        }, {
                            "period": "YYYY",
                            "format": "YYYY"
                        }],
                    "parseDates": true,
                    "autoGridCount": false,
                    "axisColor": "#555555",
                    "gridAlpha": 0.1,
                    "gridColor": "#FFFFFF",
                    "gridCount": 50
                },
                "export": {
                    "enabled": true
                }
            });
        </script>
        <div>
        <form method="post"id="datechange" action="/all">
            <input type="text" id="date1" name="date1" value="{$date1}">〜<input type="text" id="date2" name="date2" value="{$date2}"><input type="submit" value="期間変更">
        </form>
        <form method="post"id="createCsv" action="/createCsv">
            <input type="hidden" id="date1" name="date1" value="{$date1}">
            <input type="hidden" id="date2" name="date2" value="{$date2}">
            <input type="submit" value="CSVダウンロード">
        </form>


        <!-- HTML -->
        <div id="chartdiv"></div>	
        </div>
        <div class="wrap-table">
        <table border=1>
            <thead>
                <tr>
                    <th >日付</th>
                    <th >iOSインストール</th>
                    <th >Androidインストール</th>
                    <th >iOS会員登録者（男女）</th>
                    <th >AOS会員登録者（男女）</th>
                    <th >iOS退会数（男女）</th>
                    <th >AOS退会数（男女）</th>
                    <th >累計会員数（退会者は引く） </th>
                    <th >iOS会員登録率</th>
                    <th >AOS会員登録率 </th>
                    <th >トータル会員登録率</th>
                    
                    <th >iOS DAU</th>
                    <th >AOS DAU</th>
                    <th >トータル DAU </th>
                    <th >iOS　投稿数</th>
                    <th >AOS投稿数</th>
                    <th >トータル投稿数</th>
                    <th >iOSレシピ投稿数</th>
                    <th >AOS レシピ投稿数</th>
                    <th >トータルレシピ投稿数</th>
                    <th >お気に入り数→新規</th>
                    
                    <th >累計お気に入り数</th>
                    <th >コメント数 </th>
                    <th >累計コメント数</th>
                    <th >タグ数→新規 </th>
                    <th >累計タグ数</th>
                </tr>
            </thead>
            {$total7 = 0}
            {$total21 = 0}
            {$total23 = 0}
            {$total25 = 0}
            {foreach from=$datas item=aja name=pero}
                {if $aja.month neq 1}
                    {$total7 = $total7 + $aja.totalLK}
                    {$total21 = $total21 + $aja.listFavorite}
                    {$total23 = $total23 + $aja.listComment}
                    {$total25 = $total25 + $aja.listTag}
                {/if}
                
                <tr align=center style="{if $aja.month eq 1} color:red;{/if}">
                    <td>{$aja.keydate}</td>
                    <td>{$aja.installCnt}</td>
                    <td>{$aja.installCntAos}</td>
                    <td>{$aja.installCnt2}（男{$aja.installCnt2Men}・女{$aja.installCnt2Women}）</td>
                    <td>{$aja.installCntAos2}（男{$aja.installCntAos2Men}・女{$aja.installCntAos2Women}）</td>
                    <td>{$aja.deactiveIosCnt}（男{$aja.deactiveIosCntMen}・女{$aja.deactiveIosCntWomen}）</td>
                    <td>{$aja.deactiveAosCnt}（男{$aja.deactiveAosCntMen}・女{$aja.deactiveAosCntWomen}）</td>
                    <td>{$total7}</td>
                    <td>{if $aja.installCnt2 eq 0}0{else}{(($aja.installCnt)/($aja.installCnt2))|string_format:"%.2f"}{/if}</td>
                    <td>{if $aja.installCntAos2 eq 0}0{else}{$aja.installCntAos/$aja.installCntAos2|string_format:"%.2f"}{/if}</td>
                    <td>{if ($aja.installCnt2 + $aja.installCntAos2) eq 0}0{else}{(($aja.installCnt + $aja.installCntAos)/($aja.installCnt2 + $aja.installCntAos2))|string_format:"%.2f"}{/if}</td>
                    
                    <td>{$aja.loginCnt}</td>
                    <td>{$aja.loginCntAos}</td>
                    <td>{$aja.loginCnt + $aja.loginCntAos}</td>
                    <td>{$aja.listCnt}</td>
                    <td>{$aja.listCntAos}</td>
                    <td>{$aja.listCnt + $aja.listCntAos}</td>
                    <td>{$aja.listCnt2}</td>
                    <td>{$aja.listCntAos2}</td>
                    <td>{$aja.listCnt2 + $aja.listCntAos2}</td>
                    
                    <td>{$aja.listFavorite}</td>
                    
                    <td>{$total21}</td>
                    <td>{$aja.listComment}</td>
                    <td>{$total23}</td>
                    <td>{$aja.listTag}</td>
                    <td>{$total25}</td>
                </tr>
            {/foreach}
        </table>
        </div>
    </body>
</html>