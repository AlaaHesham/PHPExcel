<html>
    <head>
        <title> php </title>

        <style>
            .xmlTable{
                background: #E8D3B9;
                text-align: center;
            }

            .jsonTable{
                background: #EEEE9B;
                text-align: center;
            }
        </style>
    </head>
    <body>

        
        <?php 
        require "Classes/PHPExcel.php";

        $path="arrivals.xls";
        $reader= PHPExcel_IOFactory::createReaderForFile($path);
        $excel_Obj = $reader->load($path);

        $worksheet=$excel_Obj->getSheet('0');


        $lastRow = $worksheet->getHighestRow();
        $colomncount = $worksheet->getHighestDataColumn();
        $colomncount_number=PHPExcel_Cell::columnIndexFromString($colomncount);

        

        //columns names

        // $col_names;

        // for($col=0;$col<$colomncount_number;$col++){
		// 	$col_names[$col] = $worksheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col)."1")->getValue();
			
		// }

        $xml= "<arrival>";
        $json = "[";
        $row=1;
        $col=0;
	    for($row=1;$row<=$lastRow;$row++){
            $xml.= "<row>";
            $json .= "{";
		for($col=0;$col<$colomncount_number;$col++){
			$xml.= "<col>";
            $json .= '"col_'.$col.'"' . ': "';

			$xml.= $worksheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFormattedValue();
			$json.= $worksheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFormattedValue();

            $json .= '"';
			$xml.= "</col>";
            if($col+1<$colomncount_number)
                $json .= ',';
		}
		$xml.= "</row>";
        $json .= "}";
        if($row+1<=$lastRow)
            $json .=',';
        
	    }	
        $xml.= "</arrival>";
        $json .= "]";
        
         
        ?>

        
        <?php
        //reading xml
        echo "<h2>XML Version:</h2>";
        $xmlDoc = new DOMDocument();
        $xmlDoc->encoding = 'utf-8';

		$xmlDoc->xmlVersion = '1.0';

		$xmlDoc->formatOutput = true;
        $xmlDoc->loadXML($xml);
        

        //print $xmlDoc->saveXML();

        $x = $xmlDoc->documentElement;
        $String ="<table border=1 >";
        foreach ($x->childNodes AS $row) {
            $String .="<tr>";
            foreach ($row->childNodes AS $col){
                $String .="<td class='xmlTable'>";
                $String .= $col->nodeValue. "<br>";
                $String .="</td>";
            }
            $String .="</tr>";
        }
        $String .="</table>";

        echo $String;
        ?>

        <div id="dom-target" style="display: none;">
           <?php
            
            echo htmlspecialchars($json); /* You have to escape because the result
                                           will not be valid HTML otherwise. */
        ?>
</div>
        <?php
        //reading JSON
        echo "<h2> JSON Version </h2>";
        echo " 
            <script>
            var arrivals = JSON.parse(document.getElementById('dom-target').innerHTML);
            
            content='<table border=1>';
            for(i=0;i<arrivals.length;i++){
                content+='<tr>';
                for(j=0;j<Object.keys(arrivals[0]).length;j++){
                    
                    content+= '<td class=jsonTable>'+Object.values(arrivals[i])[j] + '</td>';
                    
                }
                content+='</tr>';
            }
            content+= '</table>';
            document.write(content);
            </script>
        
        
        ";


        ?>

        
        
    </body>
</html>
