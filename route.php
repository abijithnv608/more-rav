<?php

  
	include("db_pdo_rms.php");
	include("db_pdo_local.php");
	
	
	
		if ($_SERVER['REQUEST_METHOD'] === 'POST' and isset($_POST['option']))
		{
			if($_POST['option']=='grnCode')
			{
				$db=new DB_LOCAL();
				$db=$db->connect();
				try {
				$db->beginTransaction();
			    $query=$db->prepare("insert into grn_number values(null)" );
				$query->execute();
				$code=$db->lastInsertId();
				$db->commit();
				
				 echo json_encode($code);
				  } catch(PDOExecption $e) { 
        $db->rollback(); 
        print "Error!: " . $e->getMessage() . "</br>"; 
    } 
			}
			
			else if($_POST['option']=='login')
			{
				
				echo json_encode('success');
			}
			else if($_POST['option']=='getItemCodes')
			{
				$db=new DB_LOCAL();
				$db=$db->connect();
			    $query=$db->prepare("select item  from item_master limit 500" );
				$query->execute();
				$result=$query->fetchAll(PDO::FETCH_ASSOC);
				$data=[];
				foreach($result as $val)
				{
					$data[$val['item']]='';
					
				}
				echo json_encode($data);
				
			}
			else if($_POST['option']=='getItemDescriptions')
			{
				
				$db=new DB_LOCAL();
				$db=$db->connect();
			    $query=$db->prepare("select item_desc  from item_master limit 500" );
				$query->execute();
				$result=$query->fetchAll(PDO::FETCH_ASSOC);
				$data=[];
				foreach($result as $val)
				{
					$data[$val['item_desc']]='';
					
				}
				echo json_encode($data);
				
			}
			else if($_POST['option']=='getItemDetails')
			{
				if($_POST['input']=='code')
				{
					$item_code=$_POST['value'];
					$db=new DB_LOCAL();
				$db=$db->connect();
					$query=$db->prepare("select item_desc,standard_uom from item_master where item=:item_code" );
					$query->execute([':item_code'=>$item_code]);
					$result=$query->fetchAll(PDO::FETCH_ASSOC);
					echo json_encode($result);
				}
				else if($_POST['input']=='description')
				{
					$item_desc=$_POST['value'];
					$db=new DB_LOCAL();
				    $db=$db->connect();
					$query=$db->prepare("select item,standard_uom  from item_master where item_desc=:item_desc" );
					$query->execute([':item_desc'=>$item_desc]);
					$result=$query->fetchAll(PDO::FETCH_ASSOC);
					echo json_encode($result);
				}
				
			}
			else if($_POST['option']=='getSupplierCode')
			{
				$db=new DB_LOCAL();
				$db=$db->connect();
			    $query=$db->prepare("select  vendor supplier_code from grn_vendors " );
				$query->execute();
				$result=$query->fetchAll(PDO::FETCH_ASSOC);
				$data=[];
				foreach($result as $val)
				{
					$data[$val['supplier_code']]='';
					
				}
				echo json_encode($data);
				
			}
			else if($_POST['option']=='getSupplierName')
			{
				$db=new DB_LOCAL();
				$db=$db->connect();
			    $query=$db->prepare("select  vendor_name supplier_name from grn_vendors " );
				$query->execute();
				$result=$query->fetchAll(PDO::FETCH_ASSOC);
				$data=[];
				foreach($result as $val)
				{
					$data[$val['supplier_name']]='';
					
				}
				echo json_encode($data);
				
			}
			else if($_POST['option']=='getSupllierDetails')
			{
				if($_POST['input']=='code')
				{
					$supp=$_POST['value'];
					$db=new DB_LOCAL();
				$db=$db->connect();
					$query=$db->prepare("select  vendor_name supplier_name from grn_vendors where vendor=:supplier" );
					$query->execute([':supplier'=>$supp]);
					$result=$query->fetchAll(PDO::FETCH_ASSOC);
					echo json_encode($result);
				}
				else if($_POST['input']=='name')
				{
					$sup_name=$_POST['value'];
					$db=new DB_LOCAL();
				    $db=$db->connect();
					$query=$db->prepare("select  vendor supplier_code from grn_vendors where vendor_name=:sup_name" );
					$query->execute([':sup_name'=>$sup_name]);
					$result=$query->fetchAll(PDO::FETCH_ASSOC);
					echo json_encode($result);
				}
				
			}
			else if($_POST['option']=='addItem')
			{
				try
				{
					$db=new DB_LOCAL();
					$db=$db->connect();
					$item=$_POST['item'];
					$grnCode=$_POST['grn_code'];
					$query=$db->prepare("select grn_code from grn_master where grn_code=:grn_code" );
					$query->execute([':grn_code'=>$grnCode]);
					$result=$query->fetchAll(PDO::FETCH_ASSOC);
					if(count($result)==0)
					{
						$cc_code=$_POST['cc_code'];
						$vendor=$_POST['vendor_code'];
						$invoice_number=$_POST['invoiceNumber'];
						$query=$db->prepare("insert into  grn_master (grn_code,cc_code,vendor,invoice_number) values(:grn_code,:cc_code,:vendor,:invoice_number)" );
						$result=$query->execute([':grn_code'=>$grnCode,':cc_code'=>$cc_code,':vendor'=>$vendor,':invoice_number'=>$invoice_number]);
						$query=$db->prepare("insert into  grn_details (grn_code,item,cost,quantity,value) values(:grn_code,:item,:cost,:quantity,:value)" );
						$result=$query->execute([':grn_code'=>$grnCode,':item'=>$item['item'],':cost'=>$item['cost'],':quantity'=>$item['quantity'],':value'=>$item['cost']*$item['quantity']]);
						echo json_encode(['status'=>'success','message'=>'','id'=>$db->lastInsertId()]);

					
					}
					else if(count($result)==1)
					{
						$query=$db->prepare("insert into  grn_details (grn_code,item,cost,quantity,value) values(:grn_code,:item,:cost,:quantity,:value)" );
						$result=$query->execute([':grn_code'=>$grnCode,':item'=>$item['item'],':cost'=>$item['cost'],':quantity'=>$item['quantity'],':value'=>$item['cost']*$item['quantity']]);
						echo json_encode(['status'=>'success','message'=>'','id'=>$db->lastInsertId()]);
					}
						
				
				}
				catch(PDOException $e)
				{
					   $code = $e->getCode();
						$message =$code." ".$e->getMessage();
						echo json_encode(['status'=>'failed','message'=>$message]);
				}
			
				
			}
			else if($_POST['option']=='editItem')
			{
				try
				{
					$db=new DB_LOCAL();
					$db=$db->connect();
					$cost=$_POST['cost'];
					$id=$_POST['id'];
					$quantity=$_POST['quantity'];
					$query=$db->prepare("update  grn_details set quantity=:quantity,cost=:cost,value=:value where id=:id " );
					$result=$query->execute([':id'=>$id,':cost'=>$cost,':quantity'=>$quantity,':value'=>$cost*$quantity]);
					echo json_encode(['status'=>'success','message'=>'']);
				}
				catch(PDOException $e)
				{
					   $code = $e->getCode();
						$message =$code." ".$e->getMessage();
						echo json_encode(['status'=>'failed','message'=>$message]);
				}
			
				
			}
			else if($_POST['option']=='deleteItem')
			{
				try
				{
					$db=new DB_LOCAL();
					$db=$db->connect();
					$id=$_POST['id'];
					$query=$db->prepare("update  grn_details set del=1  where id=:id " );
					$result=$query->execute([':id'=>$id]);
					echo json_encode(['status'=>'success','message'=>'']);
				}
				catch(PDOException $e)
				{
					   $code = $e->getCode();
						$message =$code." ".$e->getMessage();
						echo json_encode(['status'=>'failed','message'=>$message]);
				}
			
				
			}
			else if($_POST['option']=='submitGRN')
			{
				try
				{
					$db=new DB_LOCAL();
					$db=$db->connect();
					
					$grn_code=$_POST['grn_code'];
					$query=$db->prepare("update  grn_master set status='Submited'  where grn_code=:grn_code and status!='Submited' " );
					$result=$query->execute([':grn_code'=>$grn_code]);
					$count = $query->rowCount();
					if($count>0)
					{
						$query=$db->prepare("select cc_code,item,quantity from grn_master a inner join grn_details b on a.grn_code=b.grn_code where a.grn_code=:grn_code" );
						$query->execute([':grn_code'=>$grn_code]);
						$result=$query->fetchAll(PDO::FETCH_ASSOC);
						foreach($result as $val)
						{
							$cc_code=$val['cc_code'];
							$item=$val['item'];
							$quantity=$val['quantity'];
							try 
							{
								$db->beginTransaction();
								$db->exec("lock table grn_soh write");
								$sql="select * from grn_soh where item='".$item."' and cc_code='".$cc_code."' for update" ;
								$query=$db->prepare($sql );
								$query->execute();
								$data=$query->fetchAll(PDO::FETCH_ASSOC);
								if(count($data)==0)
								{
									$query=$db->prepare("insert into grn_soh values('$cc_code','$item',$quantity)" );
									$query->execute();
								
								}
								
								else
								{	
									$query=$db->prepare("update grn_soh set quantity=quantity+$quantity  where cc_code='$cc_code' and item='$item'" );
									$query->execute();
									
								}
							
								$db->exec("unlock tables");
								$db->commit();
							}
							catch(PDOException $e)
							{
								$db->rollback();
								echo json_encode(['status'=>'failed','message'=>$message]);
							}
							
						}
						
					}
					
				}
				catch(PDOException $e)
				{
					   $code = $e->getCode();
						$message =$code." ".$e->getMessage();
						echo json_encode(['status'=>'failed','message'=>$message]);
				}
			
				
			}
			else if($_POST['option']=='getGrnDetails')
			{
				try
				{
					$db=new DB_LOCAL();
					$db=$db->connect();
					$grn_code=$_POST['grn_code'];
					$query=$db->prepare("select grn.id,grn.item,im.item_desc,im.standard_uom uom,grn.cost,grn.quantity from grn_details grn inner join item_master im on grn.item=im.item   where  del!=1 and grn_code=:grnCode");
					$result=$query->execute([':grnCode'=>$grn_code]);
					$result=$query->fetchAll(PDO::FETCH_ASSOC);
					echo json_encode(['status'=>'success','message'=>'','item_details'=>$result]);
				}
				catch(PDOException $e)
				{
					   $code = $e->getCode();
						$message =$code." ".$e->getMessage();
						echo json_encode(['status'=>'failed','message'=>$message]);
				}
			
				
			}
			else if($_POST['option']=='addVendor')
			{
				
				try{
					
					$db=new DB_LOCAL();
					$db=$db->connect();
					$vendor_code=$_POST['vendor_code'];
					$vendor_name=$_POST['vendor_name'];
					$query=$db->prepare("select *  from grn_vendors where vendor=:vendor_code and vendor_name=:vendor_name" );
					
					$query->execute([':vendor_code'=>$vendor_code,':vendor_name'=>$vendor_name]);
					$result=$query->fetchAll(PDO::FETCH_ASSOC);
					if(count($result)==0)
					{
						
						$query=$db->prepare("INSERT INTO grn_vendors(vendor,vendor_name) values(:vendor_code,:vendor_name)");
						$result=$query->execute([':vendor_code'=>$vendor_code,':vendor_name'=>$vendor_name]);
					}
					else
						echo json_encode(['status'=>'success','message'=>'success']);
				}
				catch(PDOException $e)
				{
					$code = $e->getCode();
					$message ="Error Code ".$code." ".$e->getMessage();
					echo json_encode(['status'=>'failed','message'=>$message]);
				}
				
				
			}
			else if($_POST['option']=='validate_item')
			{
				
				try{
					
					$db=new DB_LOCAL();
					$db=$db->connect();
					$item=$_POST['item'];
					$query=$db->prepare("select item  from item_master where item=:item" );
					$query->execute([':item'=>$item]);
					$result=$query->fetchAll(PDO::FETCH_ASSOC);
					if(count($result)==0)
					{
						
						echo json_encode(['status'=>'failed','message'=>'Incorrct Item']);
					}
					else
						echo json_encode(['status'=>'success','message'=>'success']);
				}
				catch(PDOException $e)
				{
					$code = $e->getCode();
					$message ="Error Code ".$code." ".$e->getMessage();
					echo json_encode(['status'=>'failed','message'=>$message]);
				}
				 
			}
			else if($_POST['option']=='getGrnItems')
			{
				try{
					
					$db=new DB_LOCAL();
					$db=$db->connect();
					$query=$db->prepare("select im.item,im.item_desc,standard_uom   from grn_details a inner join grn_master b on a.grn_code=b.grn_code inner join item_master im on im.item=a.item  where b.status='Submited'" );
					$query->execute();
					$result=$query->fetchAll(PDO::FETCH_ASSOC);
					$item=[];
					$item_desc=[];
					
					
					foreach($result as $val)
					{
						$item[$val['item']]='';
						$item_desc[$val['item_desc']]='';
						
					}
					$data=['data'=>$result,'item'=>$item,'item_desc'=>$item_desc];
					echo json_encode($data);
					
				}
				catch(PDOException $e)
				{
					$code = $e->getCode();
					$message ="Error Code ".$code." ".$e->getMessage();
					echo json_encode(['status'=>'failed','message'=>$message]);
				}
			}
			else if($_POST['option']=='getItems')
			{
				try{
					
					$db=new DB_LOCAL();
					$db=$db->connect();
					$query=$db->prepare("select item,item_desc,standard_uom from item_master limit 1000" );
					$query->execute();
					$result=$query->fetchAll(PDO::FETCH_ASSOC);
					$item=[];
					$item_desc=[];
					
					
					foreach($result as $val)
					{
						$item[$val['item']]='';
						$item_desc[$val['item_desc']]='';
						
					}
					$data=['data'=>$result,'item'=>$item,'item_desc'=>$item_desc];
					echo json_encode($data);
					
				}
				catch(PDOException $e)
				{
					$code = $e->getCode();
					$message ="Error Code ".$code." ".$e->getMessage();
					echo json_encode(['status'=>'failed','message'=>$message]);
				}
			}
			else if($_POST['option']=='getSupplierDetails')
			{
				try{
					
					$db=new DB_LOCAL();
					$db=$db->connect();
					$query=$db->prepare("select vendor vendorCode,vendor_name vendorName from grn_vendors" );
					$query->execute();
					$result=$query->fetchAll(PDO::FETCH_ASSOC);
					$vendorCode=[];
					$vendorName=[];
					
					
					foreach($result as $val)
					{
						$vendorCode[$val['vendorCode']]='';
						$vendorName[$val['vendorName']]='';
						
					}
					$data=['data'=>$result,'vendorCode'=>$vendorCode,'vendorName'=>$vendorName];
					echo json_encode($data);
					
				}
				catch(PDOException $e)
				{
					$code = $e->getCode();
					$message ="Error Code ".$code." ".$e->getMessage();
					echo json_encode(['status'=>'failed','message'=>$message]);
				}
			}
			else if($_POST['option']=='getStoreAllocation')
			{
				try
				{
					try
					{
						$db=new DB_LOCAL();
						$db=$db->connect();
						$item=$_POST['item'];
						$sql="
						select 
						a.id,
						b.store,
						b.store_name,
						quantity
						from 
						grn_allocation a 
						inner join store b 
						on a.store=b.store  
						where  a.status='Pending' and a.del!=1 and a.item=:item and a.cc_code=:cc_code";
						$query=$db->prepare($sql );
						$query->execute([':item'=>$item,':cc_code'=>124]);
						$result=$query->fetchAll(PDO::FETCH_ASSOC);
						$query=$db->prepare("select quantity available from grn_soh where item=:item and cc_code=:cc_code");
						$query->execute([':item'=>$item,':cc_code'=>124]);
						$available=$query->fetchAll(PDO::FETCH_ASSOC);
						$query=$db->prepare("select ifnull(sum(quantity),0) allocated from grn_allocation a where a.item=:item and a.cc_code=:cc_code and a.del!=1 and a.status='Pending' group  by cc_code,item");
						$query->execute([':item'=>$item,':cc_code'=>124]);
						$allocated=$query->fetchAll(PDO::FETCH_ASSOC);
						if(count($allocated)>0)
							$allocated=$allocated[0]['allocated'];
						else
							$allocated=0;
						if(count($available)>0)
						{
							
							$available=$available[0]['available'];
						}
						else
							$available=0;
						echo json_encode(['data'=>$result,'status'=>'success','available'=>$available,'allocated'=>$allocated]);
					}
					catch(PDOException $e)
					{
						
						$code = $e->getCode();
						$message ="Error Code ".$code." ".$e->getMessage();
						echo json_encode(['status'=>'failed','message'=>$message]);
					}
				}
				catch(Exception $e)
				{
					$code = $e->getCode();
					$message ="Error Code ".$code." ".$e->getMessage();
					echo json_encode(['status'=>'failed','message'=>$message]);
				}
				
			}
			else if($_POST['option']=='editStoreAllocation')
			{
				try
				{
					$success=true;
					
					$db=new DB_LOCAL();
					$db=$db->connect();
					$id=$_POST['id'];
					$postQuantity=$_POST['quantity'];
					$item=$_POST['item'];
					try
					{
						$db->beginTransaction();
						$query=$db->prepare("select item,cc_code,quantity from grn_allocation  where del=0 and id=:id for update" );
						$query->execute([':id'=>$id]);
						$result=$query->fetchAll(PDO::FETCH_ASSOC);
						if(count($result)>0)
						{
							$quantity=$result[0]['quantity'];
							$item=$result[0]['item'];
							$cc_code=$result[0]['cc_code'];
							if($postQuantity>$quantity)
							{
								$quantityTobeReleasedFromRepository=$postQuantity-$quantity;
								$affectedrows=$db->exec("update  grn_soh set quantity=quantity-$quantityTobeReleasedFromRepository  where item='$item' and cc_code='$cc_code' and quantity>=$quantityTobeReleasedFromRepository" );
								if($affectedrows==1)
								{
									$query=$db->prepare("update  grn_allocation set quantity=:quantity where id=:id " );
									$result=$query->execute([':id'=>$id,':quantity'=>$postQuantity]);
									
								}
								else
								{
									$sql="select quantity from grn_soh where item=:item and cc_code=:cc_code";
									$query=$db->prepare($sql );
									$query->execute([':item'=>$item,':cc_code'=>$cc_code]);	
									$avaialble_quantity=$query->fetchAll(PDO::FETCH_ASSOC);
									$success=false;
									$message="available quantity :".$avaialble_quantity[0]['quantity'];
								}
								
							}
							else if($postQuantity<$quantity)
							{
								$quantityToAddInRepository=$quantity-$postQuantity;
								$affectedrows= $db->exec("update  grn_soh set quantity=quantity+$quantityToAddInRepository  where item='$item' and cc_code='$cc_code'" );
								if($affectedrows==1)
								{
									$query=$db->prepare("update  grn_allocation set quantity=:quantity where id=:id " );
									$result=$query->execute([':id'=>$id,':quantity'=>$postQuantity]);
								}
								else
								{
										$success=false;
										$message="Error happened during updation";
								}
							}

							
						}
						$db->commit();
					}
					catch(PDOExecption $e)
					{
						$db->rollback();
					}
					$sql="
					select 
					a.id,
					b.store,
					b.store_name,
					a.quantity 
					from 
					grn_allocation a 
					inner join store b 
					on a.store=b.store  
					where  a.status='Pending' and a.del!=1 and a.item=:item";
					$query=$db->prepare($sql );
					$query->execute([':item'=>$item]);
					$result=$query->fetchAll(PDO::FETCH_ASSOC);
					$query=$db->prepare("select quantity available from grn_soh where item=:item and cc_code=:cc_code");
					$query->execute([':item'=>$item,':cc_code'=>124]);
					
					
					$available=$query->fetchAll(PDO::FETCH_ASSOC);
					$query=$db->prepare("select ifnull(sum(quantity),0) allocated from grn_allocation a where a.item=:item and a.cc_code=:cc_code and a.del!=1 and a.status='Pending' group  by cc_code,item");
					$query->execute([':item'=>$item,':cc_code'=>124]);
					$allocated=$query->fetchAll(PDO::FETCH_ASSOC);
					if(count($allocated)>0)
							$allocated=$allocated[0]['allocated'];
					else
						$allocated=0;
					if(count($available)>0)
					{
						
						$available=$available[0]['available'];
					}
					else
						$available=0;
						
					
					if($success==false)
					echo json_encode(['data'=>$result,'status'=>'failed','available'=>$avaialble[0],'allocated'=>$allocated[0],'message'=>$message]);
					else				
					echo json_encode(['data'=>$result,'status'=>'success','available'=>$available,'allocated'=>$allocated]);
				}
				catch(Exception $e)
				{
						$code = $e->getCode();
						$message =$code." ".$e->getMessage();
						echo json_encode(['status'=>'failed','message'=>$message]);
				}
			
				
			}
			else if($_POST['option']=='deleteStoreAllocation')
			{
				try
				{
					$db=new DB_LOCAL();
					$db=$db->connect();
					$db->beginTransaction();
					$id=$_POST['id'];
					$item=$_POST['item'];
					$query=$db->prepare("select item,cc_code,quantity from grn_allocation  where del=0 and id=:id for update" );
					$query->execute([':id'=>$id]);
					$result=$query->fetchAll(PDO::FETCH_ASSOC);
					if(count($result)>0)
					{
						$quantity=$result[0]['quantity'];
						$item=$result[0]['item'];
						$cc_code=$result[0]['cc_code'];
						$query=$db->prepare("update grn_allocation set del=1 where  id=:id " );
						$query->execute([':id'=>$id]);
						$db->query("update  grn_soh set quantity=quantity+$quantity  where item='$item' and cc_code='$cc_code'" );
						
					}
					$db->commit();
					$sql="
						select a.id,
						b.store,
						b.store_name,
						quantity 
						from 
						grn_allocation a 
						inner join store b 
						on a.store=b.store 
						where  a.status='Pending' and a.del!=1 and a.item=:item";
						$query=$db->prepare($sql );
						$query->execute([':item'=>$item]);
						$result=$query->fetchAll(PDO::FETCH_ASSOC);
						$query=$db->prepare("select quantity available from grn_soh where item=:item and cc_code=:cc_code");
						$query->execute([':item'=>$item,':cc_code'=>124]);
						$available=$query->fetchAll(PDO::FETCH_ASSOC);
						$query=$db->prepare("select ifnull(sum(quantity),0) allocated from grn_allocation a where a.item=:item and a.cc_code=:cc_code and a.del!=1 and a.status='Pending' group  by cc_code,item");
						$query->execute([':item'=>$item,':cc_code'=>124]);
						$allocated=$query->fetchAll(PDO::FETCH_ASSOC);
						if(count($allocated)>0)
								$allocated=$allocated[0]['allocated'];
						else
							$allocated=0;
						if(count($available)>0)
						{
							
							$available=$available[0]['available'];
						}
						else
							$available=0;
						echo json_encode(['data'=>$result,'status'=>'success','available'=>$available,'allocated'=>$allocated]);
					
				}
				catch(PDOException $e)
				{
					   $code = $e->getCode();
						$message =$code." ".$e->getMessage();
						echo json_encode(['status'=>'failed','message'=>$message]);
				}
			
				
			}
			else if($_POST['option']=='addStoreAllocation')
			{
				
					$db=new DB_LOCAL();
					$db=$db->connect();
					
					$item=$_POST['item'];
					$store=$_POST['store'];
					$quantity=$_POST['quantity'];
					$cc_code=$_POST['cc_code'];
					
							try 
							{
								$success=false;
								$db->beginTransaction();
								$sql="update grn_soh set quantity=quantity-:quantity where item=:item and cc_code=:cc_code and quantity>=:quantity";
								$query=$db->prepare($sql );
								$query->execute([':item'=>$item,':cc_code'=>$cc_code,':quantity'=>$quantity]);
								$count= $query->rowCount();
								if($count>0)
								{
								
								$query=$db->prepare("insert into grn_allocation (cc_code,store,item,quantity) values(:cc_code,:store,:item,:quantity)" );
								$query->execute([':item'=>$item,'cc_code'=>$cc_code,':store'=>$store,':quantity'=>$quantity]);
								$success=true;
								}
								else
								{
									
								$sql="select quantity from grn_soh where item=:item and cc_code=:cc_code";
								$query=$db->prepare($sql );
								$query->execute([':item'=>$item,':cc_code'=>$cc_code]);	
								$item=$query->fetchAll(PDO::FETCH_ASSOC);
								
								echo json_encode(['status'=>'failed','message'=>'Given quantity exceeded available quantity, avaialble '.$item[0]['quantity'] ]);
								}
								
								if($success==true)
								{
									$sql="select a.id,b.store,b.store_name,quantity from grn_allocation a inner join store b on a.store=b.store  where a.item=:item and a.cc_code=:cc_code and a.status='Pending'  and a.del!=1" ;
									$query=$db->prepare($sql );
									$query->execute([':item'=>$item,':cc_code'=>$cc_code]);	
									$data=$query->fetchAll(PDO::FETCH_ASSOC);
									$query=$db->prepare("select quantity available from grn_soh where item=:item and cc_code=:cc_code");
									$query->execute([':item'=>$item,':cc_code'=>124]);
									$available=$query->fetchAll(PDO::FETCH_ASSOC);
									$query=$db->prepare("select ifnull(sum(quantity),0) allocated from grn_allocation a where a.item=:item and a.cc_code=:cc_code and a.del!=1 and a.status='Pending' group  by cc_code,item");
									$query->execute([':item'=>$item,':cc_code'=>124]);
									$allocated=$query->fetchAll(PDO::FETCH_ASSOC);
									if(count($allocated)>0)
											$allocated=$allocated[0]['allocated'];
									else
										$allocated=0;
									if(count($available)>0)
									{
										
										$available=$available[0]['available'];
									}
									else
										$available=0;
									echo json_encode(['data'=>$data,'status'=>'success','available'=>$available,'allocated'=>$allocated]);
											
								}
								$db->commit();
							}
							catch(PDOException $e)
							{
								
								$db->rollback();
								$code = $e->getCode();
								$message ="Error Code ".$code." ".$e->getMessage();
								echo json_encode(['status'=>'failed','message'=>$message]);
							}
							
			}
			else if($_POST['option']=='getStores')
			{
				try{
					
					$db=new DB_LOCAL();
					$db=$db->connect();
					$query=$db->prepare("select store,store_name from store" );
					$query->execute();
					$result=$query->fetchAll(PDO::FETCH_ASSOC);
					$store=[];
					$store_name=[];
					
					
					foreach($result as $val)
					{
						$store[$val['store']]='';
						$store_name[$val['store_name']]='';
						
					}
					$data=['data'=>$result,'store'=>$store,'store_name'=>$store_name];
					echo json_encode($data);
					
				}
				catch(PDOException $e)
				{
					$code = $e->getCode();
					$message ="Error Code ".$code." ".$e->getMessage();
					echo json_encode(['status'=>'failed','message'=>$message]);
				}
			}
			
			
			
			
			
		}
		
		else if ($_SERVER['REQUEST_METHOD'] === 'GET' and isset($_GET['option']))
		{
		
		
			if($_POST['option']=='last')
			{
				$message= $_POST['message'];
				$user_name= $_POST['user_name'];
				$controller =new Controller();
				$controller->insert_message($user_name,$message);	
			}
		
		}
		
		
		
?>
