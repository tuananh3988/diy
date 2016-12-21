<html>
<meta charset="utf-8">
<body >
    <a href="/">戻る</a></br>
    <table border=1>
    <tr>
        <td >ユーザーID</td>
        <td >{$data.id}</td>
    </tr>
    <tr>
        <td >ユーザー識別子</td>
        <td >{$data.uuid}</td>
    </tr>
    <tr>
        <td >名前</td>
        <td >{$data.name}</td>
    </tr>
    <tr>
        <td >一言</td>
        <td >{$data.hitokoto}</td>
    </tr>
    <tr>
        <td >背景画像</td>
        <td ><a href="{$data.back_ground_image}"><img src="{$data.back_ground_image}" width="100px" height="100px"></a></td>
    </tr>
    <tr>
        <td >アイコン画像</td>
        <td ><a href="{$data.image}"><img src="{$data.image}" width="100px" height="100px"></a></td>
    </tr>
    <tr>
        <td >ログイン回数</td>
        <td >{$data.login_count}</td>
    </tr>
    <tr>
        <td >ログイン種別</td>
        <td >{if $data.login_type eq 1 }メール{/if}{if $data.login_type eq 2 }フェイスブック{/if}{if $data.login_type eq 3 }twitter{/if}{if $data.login_type eq 4 }google{/if}</td>
    </tr>
    <tr>
        <td >メールアドレス</td>
        <td >{$data.mail}</td>
    </tr>
    <tr>
        <td >性別</td>
        <td >{if $data.sex eq 0 }男{/if}{if $data.sex eq 1 }女{/if}</td>
    </tr>
    <tr>
        <td >最終ログイン</td>
        <td >{$data.lastLogin}</td>
    </tr>
    <tr>
        <td >インストール日</td>
        <td >{$data.install}</td>
    </tr>
	</table>
</body>
</html>