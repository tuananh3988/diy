<html>
<meta charset="utf-8">
<!-- Resources -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<!-- Chart code -->
<script>
  function deleteList(id){
    	$("#row-"+id).remove();
    	$.getJSON("http://59.106.209.199:1111/deletelist", {
    			listID:id
    		}, 
    		function(json){
    		}
    	); 
  }
</script>

<body >
    <a href="/">戻る</a></br>
    <table border=1>
    <thead>
    <tr>
    <th >投稿ID</th>
    <th >投稿ユーザーID</th>
    <th >画像</th>
    <th >投稿タイプ</th>
    <th >投稿者</th>
    <th >テキスト</th>
    <th >投稿日</th>
    <th >削除</th>
    </tr>
    </thead>
	{foreach from=$data item=aja name=pero}
	<tr align=center id="row-{$aja.id}">
		<td>{$aja.id}</td>
		<td>{$aja.mtb_user_id}</td>
		<td><img src="{$aja.image}" width="200px"></td>
		<td>{$aja.type}</td>
		<td>{$aja.name}</td>
		<td>{$aja.text}</td>
		<td>{$aja.created}</td>
		<td><input type="button" onclick="deleteList({$aja.id})" value="削除"></td>
	</tr>
	{/foreach}
	</table>
	<a href="?page={$next}">次へ</a>
</body>
</html>