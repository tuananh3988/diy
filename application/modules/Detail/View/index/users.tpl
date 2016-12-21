<html>
<meta charset="utf-8">
<body >
    <a href="/">戻る</a></br>
    <table border=1>
    <thead>
    <tr>
    <th >ユーザーID</th>
    <th >名前</th>
    <th >最終ログイン</th>
    <th >インストール日</th>
    <th >詳細</th>
    </tr>
    </thead>
	{foreach from=$data item=aja name=pero}
	<tr align=center>
		<td>{$aja.id}</td>
		<td>{$aja.name}</td>
		<td>{$aja.lastLogin}</td>
		<td>{$aja.install}</td>
		<td><a href="userdetail?id={$aja.id}">詳細</a></td>
	</tr>
	{/foreach}
	</table>
	<a href="?page={$next}">次へ</a>
</body>
</html>