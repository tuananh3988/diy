<html>
<meta charset="utf-8">
<!-- Resources -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<!-- Chart code -->
<script>
  function deleteTag(id){
    	$("#row-"+id).remove();
    	$.getJSON("http://59.106.209.199:1111/deleteTag", {
    			tagID:id
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
    <th >タグID</th>
    <th >名前</th>
    <th >削除</th>
    </tr>
    </thead>
	{foreach from=$data item=aja name=pero}
	<tr align=center id="row-{$aja.id}">
		<td>{$aja.id}</td>
		<td>{$aja.tag}</td>
		<td><input type="button" onclick="deleteTag({$aja.id})" value="削除"></td>
	</tr>
	{/foreach}
	</table>
	<a href="?page={$next}">次へ</a>
</body>
</html>