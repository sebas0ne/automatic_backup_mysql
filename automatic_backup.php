<?php
	$host="localhost";
	$username="root";
	$password="password";
	$database_name="mybd";

	$conn=mysqli_connect($host,$username,$password,$database_name);

	$tables=array();
	$sql="SHOW TABLES";
	$result=mysqli_query($conn,$sql);

	while($row=mysqli_fetch_row($result)){
		
		$tables[]=$row[0];
		
	}

	$sentenceBackup="";
	foreach($tables as $table){
		
		$query="SHOW CREATE TABLE $table";
		$result=mysqli_query($conn,$query);
		$row=mysqli_fetch_row($result);
		$sentenceBackup.="\n\n".$row[1].";\n\n";

		$query="SELECT * FROM $table";
		$result=mysqli_query($conn,$query);

		$columnCount=mysqli_num_fields($result);

		for($i=0;$i<$columnCount;$i++){
			
		while($row=mysqli_fetch_row($result)){
			
		$sentenceBackup.="INSERT INTO $table VALUES(";
		
		for($j=0;$j<$columnCount;$j++){
			
			$row[$j]=$row[$j];

			if(isset($row[$j])){
				
			$sentenceBackup.='"'.$row[$j].'"';
			
			}else{
				
			$sentenceBackup.='""';
			
			}
			
			if($j<($columnCount-1)){
				
			$sentenceBackup.=',';
			
			}
		
		}
		
		$sentenceBackup.=");\n";
		
		}
		
		}
		
		$sentenceBackup.="\n";
	}

	if(!empty($sentenceBackup)){
		
		$backup_file_name=$database_name.'_backup_'.time().'.sql';
		$fileHandler=fopen($backup_file_name,'w+');
		$number_of_lines=fwrite($fileHandler,$sentenceBackup);
		fclose($fileHandler);

		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($backup_file_name));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: '.filesize($backup_file_name));
		ob_clean();
		flush();
	}
