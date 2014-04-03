<?php

function dynamicaction($obj) {
	global
		$link,
		$kind,
		$id,
		$prefix,
		$uploads,
		$trouble,
		$trouble2,
		$actiontype,
		$valuestype,
		$redirect_path;

	$messages_success=array();
	$messages_error=array();
	$messages_information=array();
	$redirect_path='';
	$uploads_made=false;

	$this_type=$obj->getType();
	$this_right=$obj->getRight();
	$this_table=$obj->getTable();
	$this_word2=$obj->getWord2();
	if($this_type==3)
	{
		$this_word4=$obj->getWord4();
	}
	if($valuestype==1)
	{
		$filds=$obj->getElems2();
	}
	else
	{
		$filds=$obj->getElems();
	}
	$trouble=false;
	$trouble2=Array();
	if($this_type==1 && $actiontype=="change")
	{
		$linecount=encode_to_cp1251($_POST["linecount"]);
	}
	$thepas='';

	$nessfield=Array();
	$maxminchar=Array();
	if($this_type==1)
	{
		$unmatchedpassword=Array();
		$logintaken=Array();
		$olddata=Array();
		$bademail=Array();
	}

	if(($actiontype=="add" && $this_right->getAdd()) || ($actiontype=="change" && $this_right->getChange()))
	{
		if($this_type==1 && $actiontype=="change")
		{
			for($kz=1;$kz<$linecount;$kz++)
			{
				foreach($filds as $f=>$v)
				{
					if(true)
					{
						if($_POST[$v->getName()][$kz]=='' && $_POST[$v->getName()]["line".$kz]=='' && $v->getMustBe()==true && $v->getWrite()<=$this_right->getRights() && $v->getType()!="multiselect" && $v->getType()!="calendar" && $v->getType()!="h1" && !(($v->getType()=="password" || $v->getType()=="password2") && $actiontype=="change"))
						{
							if($v->getType()=="file" && ($_FILES[$v->getName()][$kz]['name']!='' || $actiontype=="change"))
							{
							}
							else
							{
								$nessfield[]=Array($v->getSname(),$kz);
								if($actiontype=="add")
								{
									$trouble2[-1]=true;
								}
								else
								{
									$trouble2[encode_to_cp1251($_POST["id"][$kz])]=true;
								}
							}
						}
						elseif($v->getWrite()<=$this_right->getRights())
						{
							if($v->getType()=="multiselect")
							{
								$val='-';
								if($v->getOne())
								{
									$val.=$_POST[$v->getName()]["line".$kz].'-';
								}
								else
								{
									$values=$v->getValues();
									for($t=0;$t<count($values);$t++)
									{
										if($_POST[$v->getName()][$values[$t][0]]["line".$kz]=="on")
										{
											$val.=$values[$t][0].'-';
										}
									}
								}
								if($v->getMustBe() && ($val=='-' || $val=='--'))
								{
									$nessfield[]=Array($v->getSname(),$kz);
									if($actiontype=="add")
									{
										$trouble2[-1]=true;
									}
									else
									{
										$trouble2[encode_to_cp1251($_POST["id"][$kz])]=true;
									}
								}
								else
								{
									if(!$v->getVirtual())
									{
										$temp[$v->getName()][$kz]=$val;
									}
									else
									{
										$virtuality[$kz].='['.$v->getName().']['.$val.']&lt;br&gt;';
									}
								}
							}
							elseif($v->getType()=="password" || $v->getType()=="password2")
							{
								if($pass[$kz]=='' && !$rr[$kz])
								{
									$pass[$kz]=encode_to_cp1251($_POST[$v->getName()][$kz]);
									$rr[$kz]=true;
									if($v->getType()=="password")
									{
										$thepas[$kz]=$v->getName();
									}
								}
								else
								{
									if($v->getType()=="password")
									{
										$thepas[$kz]=$v->getName();
									}
									$pass2[$kz]=encode_to_cp1251($_POST[$v->getName()][$kz]);
									if($pass2[$kz]!=$pass[$kz])
									{
										$unmatchedpassword[]=$kz;
										if($actiontype=="add")
										{
											$trouble2[-1]=true;
										}
										else
										{
											$trouble2[$_POST["id"][$kz]]=true;
										}
									}
									else
									{
										if($pass[$kz]!='')
										{
											if(strlen($pass[$kz])>=$v->getMinchar() && strlen($pass[$kz])<=$v->getMaxchar())
											{
												if(!$v->getVirtual())
												{
													$temp[$thepas[$kz]][$kz]=encode_to_cp1251(md5($pass[$kz]));
												}
												else
												{
													$virtuality[$kz].='['.$v->getName().']['.encode_to_cp1251(md5($pass[$kz])).']&lt;br&gt;';
												}
											}
											else
											{
												$maxminchar[]=Array($v->getSname(),$kz);
												if($actiontype=="add")
												{
													$trouble2[-1]=true;
												}
												else
												{
													$trouble2[$_POST["id"][$kz]]=true;
												}
											}
										}
									}
								}
							}
							elseif($v->getType()=="email")
							{
								$em=$v->check_email($_POST[$v->getName()][$kz]);
								if($em!="OK")
								{
									$bademail[]=Array($v->getSname(),$kz);
									if($actiontype=="add")
									{
										$trouble2[-1]=true;
									}
									else
									{
										$trouble2[$_POST["id"][$kz]]=true;
									}
								}
								else
								{
									if(!$v->getVirtual())
									{
										$temp[$v->getName()][$kz]=encode_to_cp1251($_POST[$v->getName()][$kz]);
									}
									else
									{
										$virtuality[$kz].='['.$v->getName().']['.encode_to_cp1251($_POST[$v->getName()][$kz]).']&lt;br&gt;';
									}
								}
							}
							elseif($v->getType()=="calendar")
							{
								if(!$v->getVirtual())
								{
									$temp[$v->getName()][$kz]=date("Y-m-d",strtotime($_POST[$v->getName()][$kz]));
								}
								else
								{
									$virtuality[$kz].='['.$v->getName().']['.date("Y-m-d",strtotime($_POST[$v->getName()][$kz])).']&lt;br&gt;';
								}
							}
							elseif($v->getType()=="checkbox")
							{
								if(!$v->getVirtual())
								{
									if($_POST[$v->getName()][$kz]=='on')
									{
										$temp[$v->getName()][$kz]=1;
									}
									else
									{
										$temp[$v->getName()][$kz]=0;
									}
								}
								else
								{
									if($_POST[$v->getName()][$kz]=='on')
									{
										$virtuality[$kz].='['.$v->getName().'][1]&lt;br&gt;';
									}
									else
									{
										$virtuality[$kz].='['.$v->getName().'][0]&lt;br&gt;';
									}
								}
							}
							elseif($v->getType()=="number")
							{
								$number=encode_to_cp1251($_POST[$v->getName()][$kz]);
								//settype($number, "float");
								if(!is_numeric($number))
								{
									$number=0;
								}
								if($v->getRound())
								{
									$number=round($number);
								}
								if(!$v->getVirtual())
								{
									$temp[$v->getName()][$kz]=$number;
								}
								else
								{
									$virtuality[$kz].='['.$v->getName().']['.$number.']&lt;br&gt;';
								}
							}
							elseif($v->getType()=="login")
							{
								$result=mysql_query("SELECT * FROM ".$this_table." WHERE ".$v->getName()."='".$_POST[$v->getName()][$kz]."' AND id!=".$_POST["id"][$kz]);
								$a=mysql_fetch_array($result);
								$login=encode_to_cp1251($_POST[$v->getName()][$kz]);
								if($a[$v->getName()]!='')
								{
									$logintaken[]=$kz;
									if($actiontype=="add")
									{
										$trouble2[-1]=true;
									}
									else
									{
										$trouble2[$_POST["id"][$kz]]=true;
									}
								}
								else
								{
									if(strlen($login)>=$v->getMinchar() && strlen($login)<=$v->getMaxchar())
									{
										if(!$v->getVirtual())
										{
											$temp[$v->getName()][$kz]=$login;
										}
										else
										{
											$virtuality[$kz].='['.$v->getName().']['.$login.']&lt;br&gt;';
										}
									}
									else
									{
										$maxminchar[]=Array($v->getSname(),$kz);
										if($actiontype=="add")
										{
											$trouble2[-1]=true;
										}
										else
										{
											$trouble2[$_POST["id"][$kz]]=true;
										}
									}
								}
							}
							elseif($v->getType()=="timestamp")
							{
								$result=mysql_query("SELECT * FROM ".$this_table." WHERE id=".encode_to_cp1251($_POST["id"][$kz]));
								$a=mysql_fetch_array($result);
								if(encode_to_cp1251($_POST[$v->getName()][$kz])<$a[$v->getName()])
								{
									$olddata[]=$kz;
									if($actiontype=="add")
									{
										$trouble2[-1]=true;
									}
									else
									{
										$trouble2[$_POST["id"][$kz]]=true;
									}
								}
								else
								{
									if(!$v->getVirtual())
									{
										$temp[$v->getName()][$kz]=time();
									}
									else
									{
										$virtuality[$kz].='['.$v->getName().']['.time().']&lt;br&gt;';
									}
								}
							}
							elseif($v->getType()!="file" && $v->getType()!="h1")
							{
								if($v->getType()=="text" || $v->getType()=="textarea" || $v->getType()=="wysiwyg")
								{
									if(!(($v->getMinchar()!='' && strlen($_POST[$v->getName()][$kz])<$v->getMinchar()) || ($v->getMaxchar()!='' && strlen($_POST[$v->getName()][$kz])>$v->getMaxchar())))
									{
										if(!$v->getVirtual())
										{
											$temp[$v->getName()][$kz]=encode_to_cp1251($_POST[$v->getName()][$kz]);
										}
										else
										{
											$virtuality[$kz].='['.$v->getName().']['.encode_to_cp1251($_POST[$v->getName()][$kz]).']&lt;br&gt;';
										}
									}
									else
									{
										$maxminchar[]=Array($v->getSname(),$kz);
										if($actiontype=="add")
										{
											$trouble2[-1]=true;
										}
										else
										{
											$trouble2[$_POST["id"][$kz]]=true;
										}
									}
								}
								else
								{
									if(!$v->getVirtual())
									{
										$temp[$v->getName()][$kz]=encode_to_cp1251($_POST[$v->getName()][$kz]);
									}
									else
									{
										$virtuality[$kz].='['.$v->getName().']['.encode_to_cp1251($_POST[$v->getName()][$kz]).']&lt;br&gt;';
									}
								}
							}
						}
					}
				}
			}
		}
		else
		{
			foreach($filds as $f=>$v)
			{
				//if(!$trouble)
				if(true)
				{
					if(encode_to_cp1251($_POST[$v->getName()])=='' && $v->getMustBe()==true && $v->getWrite()<=$this_right->getRights() && $v->getType()!="multiselect" && $v->getType()!="calendar" && $v->getType()!="h1" && !(($v->getType()=="password" || $v->getType()=="password2") && $actiontype=="change"))
					{
						if($v->getType()=="file" && ($_FILES[$v->getName()]['name']!='' || $actiontype=="change"))
						{
						}
						else
						{
							$nessfield[]=Array($v->getSname());
							$trouble=true;
							if($obj->getType()==1)
							{
								if($actiontype=="add")
								{
									$trouble2[-1]=true;
								}
								else
								{
									$trouble2[encode_to_cp1251($_POST["id"])]=true;
								}
							}
							else {
								$trouble2[]=$v->getName();
							}
						}
					}
					elseif($v->getWrite()<=$this_right->getRights())
					{
						if($v->getType()=="multiselect")
						{
							$val='-';
							if($v->getOne())
							{
								$val.=$_POST[$v->getName()].'-';
							}
							else
							{
								$values=$v->getValues();
								for($t=0;$t<count($values);$t++)
								{
									if($_POST[$v->getName()][$values[$t][0]]=="on")
									{
										$val.=$values[$t][0].'-';
									}
								}
							}
							if($v->getMustBe() && ($val=='-' || $val=='--'))
							{
								$nessfield[]=Array($v->getSname());
								$trouble=true;
								if($obj->getType()==1)
								{
									if($actiontype=="add")
									{
										$trouble2[-1]=true;
									}
									else
									{
										$trouble2[encode_to_cp1251($_POST["id"])]=true;
									}
								}
								else {
									$trouble2[]=$v->getName();
								}
							}
							else
							{
								if(!$v->getVirtual())
								{
									$temp[$v->getName()]=$val;
								}
								else
								{
									$virtuality.='['.$v->getName().']['.$val.']&lt;br&gt;';
								}
							}
						}
						elseif($v->getType()=="password" || $v->getType()=="password2")
						{
							if($pass=='' && !$rr)
							{
								$pass=encode_to_cp1251($_POST[$v->getName()]);
								$rr=true;
								if($v->getType()=="password")
								{
									$thepas=$v->getName();
								}
								if(strlen($pass)<$v->getMinchar() || strlen($pass)>$v->getMaxchar())
								{
									if(!($actiontype=="change" && $pass==''))
									{
										$maxminchar[]=Array($v->getSname());
										$trouble=true;
										if($obj->getType()==1)
										{
											if($actiontype=="add")
											{
												$trouble2[-1]=true;
											}
											else
											{
												$trouble2[encode_to_cp1251($_POST["id"])]=true;
											}
										}
										else {
											$trouble2[]=$v->getName();
										}
									}
								}
							}
							else
							{
								if($v->getType()=="password")
								{
									$thepas=$v->getName();
								}
								$pass2=encode_to_cp1251($_POST[$v->getName()]);
								if($pass2!=$pass)
								{
									$messages_error[]='Несовпадение пароля в основном и в проверочном полях.';
									$trouble=true;
									if($obj->getType()==1)
									{
										if($actiontype=="add")
										{
											$trouble2[-1]=true;
										}
										else
										{
											$trouble2[encode_to_cp1251($_POST["id"])]=true;
										}
									}
									else {
										$trouble2[]=$v->getName();
									}
								}
								else
								{
									if($pass!='')
									{
										if(strlen($pass)>=$v->getMinchar() && strlen($pass)<=$v->getMaxchar())
										{
											if(!$v->getVirtual())
											{
												$temp[$thepas]=encode_to_cp1251(md5($pass));
											}
											else
											{
												$virtuality.='['.$v->getName().']['.encode_to_cp1251(md5($pass)).']&lt;br&gt;';
											}
										}
										else
										{
											$maxminchar[]=Array($v->getSname());
											$trouble=true;
											if($obj->getType()==1)
											{
												if($actiontype=="add")
												{
													$trouble2[-1]=true;
												}
												else
												{
													$trouble2[encode_to_cp1251($_POST["id"])]=true;
												}
											}
											else {
												$trouble2[]=$v->getName();
											}
										}
									}
								}
							}
						}
						elseif($v->getType()=="email")
						{
							$em=$v->check_email($_POST[$v->getName()]);
							if($em!="OK")
							{
								$messages_error[]=$em;
								$trouble=true;
								if($obj->getType()==1)
								{
									if($actiontype=="add")
									{
										$trouble2[-1]=true;
									}
									else
									{
										$trouble2[encode_to_cp1251($_POST["id"])]=true;
									}
								}
								else {
									$trouble2[]=$v->getName();
								}
							}
							else
							{
								if(!$v->getVirtual())
								{
									$temp[$v->getName()]=encode_to_cp1251($_POST[$v->getName()]);
								}
								else
								{
									$virtuality.='['.$v->getName().']['.encode_to_cp1251($_POST[$v->getName()]).']&lt;br&gt;';
								}
							}
						}
						elseif($v->getType()=="calendar")
						{
							if(!$v->getVirtual())
							{
								$temp[$v->getName()]=date("Y-m-d",strtotime($_POST[$v->getName()]));
							}
							else
							{
								$virtuality.='['.$v->getName().']['.date("Y-m-d",strtotime($_POST[$v->getName()])).']&lt;br&gt;';
							}
						}
						elseif($v->getType()=="checkbox")
						{
							if(!$v->getVirtual())
							{
								if($_POST[$v->getName()]=='on')
								{
									$temp[$v->getName()]=1;
								}
								else
								{
									$temp[$v->getName()]=0;
								}
							}
							else
							{
								if($_POST[$v->getName()]=='on')
								{
									$virtuality.='['.$v->getName().'][1]&lt;br&gt;';
								}
								else
								{
									$virtuality.='['.$v->getName().'][0]&lt;br&gt;';
								}
							}
						}
						elseif($v->getType()=="number")
						{
							$number=encode_to_cp1251($_POST[$v->getName()]);
							//settype($number, "float");
							if(!is_numeric($number))
							{
								$number=0;
							}
							if($v->getRound())
							{
								$number=round($number);
							}
							if(!$v->getVirtual())
							{
								$temp[$v->getName()]=$number;
							}
							else
							{
								$virtuality.='['.$v->getName().']['.$number.']&lt;br&gt;';
							}
						}
						elseif($v->getType()=="login")
						{
							$result=mysql_query("SELECT * FROM ".$this_table." WHERE ".$v->getName()."='".$_POST[$v->getName()]."' AND id!=".$id);
							$a=mysql_fetch_array($result);
							$login=encode_to_cp1251($_POST[$v->getName()]);
							if($a[$v->getName()]!='')
							{
								$messages_error[]='Такой логин уже занят. Пожалуйста, выберите другой.';
								$trouble=true;
								if($obj->getType()==1)
								{
									if($actiontype=="add")
									{
										$trouble2[-1]=true;
									}
									else
									{
										$trouble2[encode_to_cp1251($_POST["id"])]=true;
									}
								}
								else {
									$trouble2[]=$v->getName();
								}
							}
							else
							{
								if(strlen($login)>=$v->getMinchar() && strlen($login)<=$v->getMaxchar())
								{
									if(!$v->getVirtual())
									{
										$temp[$v->getName()]=$login;
									}
									else
									{
										$virtuality.='['.$v->getName().']['.$login.']&lt;br&gt;';
									}
								}
								else
								{
									$maxminchar[]=Array($v->getSname());
									$trouble=true;
									if($obj->getType()==1)
									{
										if($actiontype=="add")
										{
											$trouble2[-1]=true;
										}
										else
										{
											$trouble2[encode_to_cp1251($_POST["id"])]=true;
										}
									}
									else {
										$trouble2[]=$v->getName();
									}
								}
							}
						}
						elseif($v->getType()=="timestamp")
						{
							$result=mysql_query("SELECT * FROM ".$this_table." WHERE id=".$id);
							$a=mysql_fetch_array($result);
							if($_POST[$v->getName()]<$a[$v->getName()])
							{
								$messages_error[]='Ваши данные устарели. Перезайдите через меню на страницу, прежде чем заново вносить изменения.<br>Вероятно, кто-то работает одновременно с вами над теми же данными.';
								$trouble=true;
								if($obj->getType()==1)
								{
									if($actiontype=="add")
									{
										$trouble2[-1]=true;
									}
									else
									{
										$trouble2[encode_to_cp1251($_POST["id"])]=true;
									}
								}
							}
							else
							{
								if(!$v->getVirtual())
								{
									$now=time();
									$temp[$v->getName()]=$now;
								}
								else
								{
									$now=time();
									$virtuality.='['.$v->getName().']['.$now.']&lt;br&gt;';
								}
							}
						}
						elseif($v->getType()!="file" && $v->getType()!="h1")
						{
							if($v->getType()=="text" || $v->getType()=="textarea" || $v->getType()=="wysiwyg")
							{
								if(!(($v->getMinchar()!='' && strlen($_POST[$v->getName()])<$v->getMinchar()) || ($v->getMaxchar()!='' && strlen($_POST[$v->getName()])>$v->getMaxchar())))
								{
									if(!$v->getVirtual())
									{
										$temp[$v->getName()]=encode_to_cp1251($_POST[$v->getName()]);
									}
									else
									{
										$virtuality.='['.$v->getName().']['.encode_to_cp1251($_POST[$v->getName()]).']&lt;br&gt;';
									}
								}
								else
								{
									$maxminchar[]=Array($v->getSname());
									$trouble=true;
									if($obj->getType()==1)
									{
										if($actiontype=="add")
										{
											$trouble2[-1]=true;
										}
										else
										{
											$trouble2[encode_to_cp1251($_POST["id"])]=true;
										}
									}
									else {
										$trouble2[]=$v->getName();
									}
								}
							}
							else
							{
								if(!$v->getVirtual())
								{
									$temp[$v->getName()]=encode_to_cp1251($_POST[$v->getName()]);
								}
								else
								{
									$virtuality.='['.$v->getName().']['.encode_to_cp1251($_POST[$v->getName()]).']&lt;br&gt;';
								}
							}
						}
					}
				}
			}
		}
	}

	if(count($maxminchar)>0)
	{
		$maxminchar2='';
		for($i=0;$i<count($maxminchar);$i++)
		{
			$maxminchar2.='«'.$maxminchar[$i][0].'»';
			if($maxminchar[$i][1]>0)
			{
				$maxminchar2.=' в строке №'.$maxminchar[$i][1];
			}
			$maxminchar2.=', ';
		}
		$maxminchar2=substr($maxminchar2,0,strlen($maxminchar2)-2).'.';
		if(count($maxminchar)>1)
		{
			$messages_error[]='Недопустимая длина текста в полях: '.$maxminchar2;
		}
		else
		{
			$messages_error[]='Недопустимая длина текста в поле '.$maxminchar2;
		}
	}

	if(count($nessfield)>0)
	{
		$nessfield2='';
		for($i=0;$i<count($nessfield);$i++)
		{
			$nessfield2.='«'.$nessfield[$i][0].'»';
			if($nessfield[$i][1]>0)
			{
				$nessfield2.=' в строке №'.$nessfield[$i][1];
			}
			$nessfield2.=', ';
		}
		$nessfield2=substr($nessfield2,0,strlen($nessfield2)-2).'.';
		if(count($nessfield)>1)
		{
			$messages_error[]='Не заполнены обязательные поля: '.$nessfield2;
		}
		else
		{
			$messages_error[]='Не заполнено обязательное поле '.$nessfield2;
		}
	}

	if(count($bademail)>0)
	{
		$bademail2='';
		for($i=0;$i<count($bademail);$i++)
		{
			$bademail2.='«'.$bademail[$i][0].'»';
			if($bademail[$i][1]>0)
			{
				$bademail2.=' в строке №'.$bademail[$i][1];
			}
			$bademail2.=', ';
		}
		$bademail2=substr($bademail2,0,strlen($bademail2)-2).'.';
		if(count($bademail)>1)
		{
			$messages_error[]='Неверный формат данных в полях: '.$bademail2;
		}
		else
		{
			$messages_error[]='Неверный формат данных в поле '.$bademail2;
		}
	}

	if(count($unmatchedpassword)>0)
	{
		$seqstart=false;
		for($i=0;$i<count($unmatchedpassword);$i++)
		{
			if($i==0)
			{
				$unmatchedpassword2=$unmatchedpassword[$i];
				if($unmatchedpassword[$i+1]==$unmatchedpassword[$i]+1 && isset($unmatchedpassword[$i+1]))
				{
					$unmatchedpassword2.='-';
					$seqstart=true;
				}
				elseif(isset($unmatchedpassword[$i+1]))
				{
					$unmatchedpassword2.=', ';
					$seqstart=false;
				}
			}
			elseif($i==count($unmatchedpassword)-1)
			{
				$unmatchedpassword2.=$unmatchedpassword[$i];
			}
			else
			{
				if($unmatchedpassword[$i+1]>$unmatchedpassword[$i]+1)
				{
					$unmatchedpassword2.=$unmatchedpassword[$i].', ';
					$seqstart=false;
				}
				elseif($unmatchedpassword[$i+1]==$unmatchedpassword[$i]+1)
				{
					if(!$seqstart)
					{
						$unmatchedpassword2.=$unmatchedpassword[$i].'-';
						$seqstart=true;
					}
				}
			}
		}
		if(count($unmatchedpassword)>1)
		{
			$messages_error[]='Несовпадение пароля в основном и в проверочном полях в строках №№'.$unmatchedpassword2;
		}
		else
		{
			$messages_error[]='Несовпадение пароля в основном и в проверочном полях в строке №'.$unmatchedpassword2;
		}
	}

	if(count($logintaken)>0)
	{
		$seqstart=false;
		for($i=0;$i<count($logintaken);$i++)
		{
			if($i==0)
			{
				$logintaken2=$logintaken[$i];
				if($logintaken[$i+1]==$logintaken[$i]+1 && isset($logintaken[$i+1]))
				{
					$logintaken2.='-';
					$seqstart=true;
				}
				elseif(isset($logintaken[$i+1]))
				{
					$logintaken2.=', ';
					$seqstart=false;
				}
			}
			elseif($i==count($logintaken)-1)
			{
				$logintaken2.=$logintaken[$i];
			}
			else
			{
				if($logintaken[$i+1]>$logintaken[$i]+1)
				{
					$logintaken2.=$logintaken[$i].', ';
					$seqstart=false;
				}
				elseif($logintaken[$i+1]==$logintaken[$i]+1)
				{
					if(!$seqstart)
					{
						$logintaken2.=$logintaken[$i].'-';
						$seqstart=true;
					}
				}
			}
		}
		if(count($logintaken)>1)
		{
			$messages_error[]='Логины, указанные в строках №№'.$logintaken2.' уже заняты. Пожалуйста, выберите другие.';
		}
		else
		{
			$messages_error[]='Логин, указанный в строке №'.$logintaken2.' уже занят. Пожалуйста, выберите другой.';
		}
	}

	if(count($olddata)>0)
	{
		$seqstart=false;
		for($i=0;$i<count($olddata);$i++)
		{
			if($i==0)
			{
				$olddata2=$olddata[$i];
				if($olddata[$i+1]==$olddata[$i]+1 && isset($olddata[$i+1]))
				{
					$olddata2.='-';
					$seqstart=true;
				}
				elseif(isset($olddata[$i+1]))
				{
					$olddata2.=', ';
					$seqstart=false;
				}
			}
			elseif($i==count($olddata)-1)
			{
				$olddata2.=$olddata[$i];
			}
			else
			{
				if($olddata[$i+1]>$olddata[$i]+1)
				{
					$olddata2.=$olddata[$i].', ';
					$seqstart=false;
				}
				elseif($olddata[$i+1]==$olddata[$i]+1)
				{
					if(!$seqstart)
					{
						$olddata2.=$olddata[$i].'-';
						$seqstart=true;
					}
				}
			}
		}
		if(count($olddata)>1)
		{
			$messages_error[]='Ваши данные в строках №№'.$olddata2.' устарели. Перезайдите через меню на страницу, прежде чем заново вносить изменения.<br>Вероятно, кто-то работает одновременно с вами над теми же данными.';
		}
		else
		{
			$messages_error[]='Ваши данные в строке №'.$olddata2.' устарели. Перезайдите через меню на страницу, прежде чем заново вносить изменения.<br>Вероятно, кто-то работает одновременно с вами над теми же данными.';
		}
	}

	if(!$trouble && !($trouble2[-1]==true && $actiontype=="add"))
	{
		$table=$this_table;

		if($obj->getVirtualField()!='')
		{
			if($this_type==1 && $actiontype=="change")
			{
				for($kz=1;$kz<$linecount;$kz++)
				{
					$temp[$obj->getVirtualField()][$kz].=$virtuality[$kz];
				}
			}
			else
			{
				$temp[$obj->getVirtualField()].=$virtuality;
			}
		}

		if($actiontype=="add")
		{
			$query="INSERT INTO ".$table." (";
			$query2="SELECT * FROM ".$table." WHERE ";
			$t='';
			$s='';
			if(count($temp)>0)
			{
				$j=0;
				for($i=0;$i<count($filds);$i++)
				{
					if($filds[$i]->getType()!="file" && $filds[$i]->getType()!="password2" && $filds[$i]->getType()!="h1" && $filds[$i]->getWrite()<=$this_right->getRights() && $filds[$i]->getVirtual()!=true)
					{
						$fildsname=$filds[$i]->getName();
						if($j<count($temp)-1)
						{
							$t.=$fildsname.', ';
							$s.="'".$temp[$fildsname]."', ";
							if($filds[$i]->getType()!="timestamp")
							{
								$q2.=$fildsname."='".$temp[$fildsname]."' AND ";
							}
							else
							{
								$q2.=$fildsname."!='' AND ";
							}
							$j++;
						}
						else
						{
							$t.=$fildsname.') VALUES (';
							$s.="'".mysql_real_escape_string($temp[$fildsname])."')";
							if($filds[$i]->getType()!="timestamp")
							{
								$q2.=$fildsname."='".$temp[$fildsname]."'";
							}
							else
							{
								$q2.=$fildsname."!=''";
							}
							$j++;
						}
					}
				}
			}
			else
			{
				$t=') VALUES ()';
				$q2='id=0';
			}
			$query.=$t.$s;
			$query2.=$q2;
			$res=mysql_query($query2);
			$res2=mysql_fetch_array($res);
			if($res2["id"]=='')
			{
				foreach($filds as $f=>$v)
				{
					if($v->getType()=="file" && $v->getMustBe())
					{
						$ext='';
						$f_name=$_FILES[$v->getName()]['name'];

						if($f_name!='')
						{
							$upload=$v->getUpload();
							$ext='';
							$j=$upload["extensions"];
							for($i=0;$i<count($j);$i++)
							{
								$test=substr($f_name,strlen($f_name)-strlen($j[$i]),strlen($j[$i]));
								if(strtoupper($j[$i])==strtoupper($test))
								{
									$ext=$j[$i];
								}
							}
							if($ext=='')
							{
								$donotcontinue=true;
								$messages_error[]='Недопустимое расширение у обязательного файла (поле «'.$v->getSname().'»).<br>Файл не загружен.';
							}
						}
						else
						{
							$donotcontinue=true;
							$messages_error[]='Ошибка при загрузке обязательного файла (поле «'.$v->getSname().'»).';
						}
					}
				}
				if($donotcontinue)
				{
					$trouble=true;
				}
				else
				{
					mysql_query($query);
					if($_SESSION["admin"] && mysql_error()!='') {
						$messages_error[]=mysql_error();
					}
					$id=mysql_insert_id($link);
					foreach($filds as $f=>$v)
					{
						if($v->getType()=="file")
						{
							uploads($v->getName(),$v->getUpload(),'change',$id);
							$uploads_made=true;
						}
					}

					if($valuestype==1)
					{
						err($this_word4[0]);
					}
					else
					{
						err($this_word2[0]);
					}
					if(function_exists('dynamic_add_success')) {
						dynamic_add_success();
					}
					redirect_construct();
				}
			}
			else
			{
				$messages_error[]='Заблокировано повторное сохранение.';
			}
		}
		elseif($actiontype=="change")
		{
			if($this_type==1)
			{
				$success=Array();
				for($kz=1;$kz<$linecount;$kz++)
				{
					if($trouble2[$_POST["id"][$kz]]==false)
					{
						$query="UPDATE ".$table." SET ";
						if(count($temp)>0)
						{
							$j=0;
							for($i=0;$i<count($filds);$i++)
							{
								if($filds[$i]->getType()!="file" && $filds[$i]->getType()!="password2" && $filds[$i]->getType()!="h1" && $filds[$i]->getWrite()<=$this_right->getRights() && $filds[$i]->getVirtual()!=true)
								{
									if(isset($temp[$filds[$i]->getName()][$kz])) {
										$query.=$filds[$i]->getName()."='".$temp[$filds[$i]->getName()][$kz]."',";
										$j++;
									}
								}
							}
							$query=substr($query,0,strlen($query)-1)." WHERE id=".encode_to_cp1251($_POST["id"][$kz]);
						}
						if($this_right->getChangeRestrict()!='')
						{
							$result=mysql_query("SELECT * FROM ".$table." WHERE ".$this_right->getChangeRestrict()." and id=".encode_to_cp1251($_POST["id"][$kz]));
							$a=mysql_fetch_array($result);
							if($a["id"]!='')
							{
								mysql_query($query);
								if($_SESSION["admin"] && mysql_error()!='') {
									$messages_error[]=mysql_error();
								}
								foreach($filds as $f=>$v)
								{
									if($v->getType()=="file")
									{
										if($_FILES[$v->getName()]['name'][$kz]!='')
										{
											uploads($v->getName(),$v->getUpload(),'change',encode_to_cp1251($_POST["id"][$kz]),$kz);
											$uploads_made=true;
										}
									}
								}
							}
						}
						else
						{
							mysql_query($query);
							if($_SESSION["admin"] && mysql_error()!='') {
								$messages_error[]=mysql_error();
							}
							foreach($filds as $f=>$v)
							{
								if($v->getType()=="file")
								{
									if($_FILES[$v->getName()]['name'][$kz]!='')
									{
										uploads($v->getName(),$v->getUpload(),'change',encode_to_cp1251($_POST["id"][$kz]),$kz);
										$uploads_made=true;
									}
								}
							}
						}

						$success[]=$kz;
					}
					//echo($query."<br>");
				}
				if(count($success)>0)
				{
					$seqstart=false;
					for($i=0;$i<count($success);$i++)
					{
						if($i==0)
						{
							$successstr=$success[$i];
							if($success[$i+1]==$success[$i]+1 && isset($success[$i+1]))
							{
								$successstr.='-';
								$seqstart=true;
							}
							elseif(isset($success[$i+1]))
							{
								$successstr.=', ';
								$seqstart=false;
							}
						}
						elseif($i==count($success)-1)
						{
							$successstr.=$success[$i];
						}
						else
						{
							if($success[$i+1]>$success[$i]+1)
							{
								$successstr.=$success[$i].', ';
								$seqstart=false;
							}
							elseif($success[$i+1]==$success[$i]+1)
							{
								if(!$seqstart)
								{
									$successstr.=$success[$i].'-';
									$seqstart=true;
								}
							}
						}
					}
					if(function_exists('dynamic_save_success')) {
						dynamic_save_success();
					}
					$messages_success[]=$this_word2[1].' в строках №№'.$successstr.'.';
				}
			}
			else
			{
				$query="UPDATE ".$table." SET ";
				if(count($temp)>0)
				{
					$j=0;
					for($i=0;$i<count($filds);$i++)
					{
						if($filds[$i]->getType()!="file" && $filds[$i]->getType()!="password2" && $filds[$i]->getType()!="h1" && $filds[$i]->getWrite()<=$this_right->getRights() && $filds[$i]->getVirtual()!=true)
						{
							if($j<count($temp)-1)
							{
								if(!($filds[$i]->getType()=="password" && $temp[$filds[$i]->getName()]==''))
								{
									$query.=$filds[$i]->getName()."='".mysql_real_escape_string($temp[$filds[$i]->getName()])."',";
									$j++;
								}
							}
							else
							{
								if(!($filds[$i]->getType()=="password" && $temp[$filds[$i]->getName()]==''))
								{
									$query.=$filds[$i]->getName()."='".mysql_real_escape_string($temp[$filds[$i]->getName()])."' WHERE id=".$id;
									$j++;
								}
							}
						}
					}
				}
				if($this_right->getChangeRestrict()!='')
				{
					$result=mysql_query("SELECT * FROM ".$table." WHERE ".$this_right->getChangeRestrict()." and id=".$id);
					$a=mysql_fetch_array($result);
					if($a["id"]!='')
					{
						mysql_query($query);
						if($_SESSION["admin"] && mysql_error()!='') {
							$messages_error[]=mysql_error();
						}
						foreach($filds as $f=>$v)
						{
							if($v->getType()=="file")
							{
								if($_FILES[$v->getName()]['name']!='')
								{
									uploads($v->getName(),$v->getUpload(),'change',$id);
									$uploads_made=true;
								}
							}
						}

						if($valuestype==1)
						{
							$messages_success[]=$this_word4[1];
						}
						else
						{
							$messages_success[]=$this_word2[1];
						}
						if(function_exists('dynamic_save_success')) {
							dynamic_save_success();
						}
					}
				}
				else
				{
					mysql_query($query);
					if($_SESSION["admin"] && mysql_error()!='') {
						$messages_error[]=mysql_error();
					}
					foreach($filds as $f=>$v)
					{
						if($v->getType()=="file")
						{
							if($_FILES[$v->getName()]['name']!='')
							{
								uploads($v->getName(),$v->getUpload(),'change',$id);
								$uploads_made=true;
							}
						}
					}

					if($valuestype==1)
					{
						$messages_success[]=$this_word4[1];
					}
					else
					{
						$messages_success[]=$this_word2[1];
					}
					if(function_exists('dynamic_save_success')) {
						dynamic_save_success();
					}
				}
			}
		}
		elseif($actiontype=="delete" && $this_right->getDelete())
		{
			$ill=encode_to_cp1251($_GET["ill"]);

			foreach($filds as $f=>$v)
			{
				if($v->getType()!='h1') {
					if($v->getName()==$ill)	{
						$t=$v->getUpload();
					}
				}
			}
			if($ill=='')
			{
				if($obj->getType()==3 && $valuestype==0)
				{
					if($this_right->getDeleteRestrict()!='')
					{
						$result=mysql_query("SELECT * FROM ".$table." WHERE ".$this_right->getDeleteRestrict()." and id=".$id);
						$a=mysql_fetch_array($result);
						if($a["id"]!='')
						{
							cleartype3($obj,$this,$this_table,$id,$obj->getElems(),$obj->getElems2(),$valuestype);
							if(function_exists('dynamic_delete_success')) {
								dynamic_delete_success();
							}
							redirect_construct();
							err($this_word2[3]);
						}
					}
					else
					{
						cleartype3($obj,$this,$this_table,$id,$obj->getElems(),$obj->getElems2(),$valuestype);
						if(function_exists('dynamic_delete_success')) {
							dynamic_delete_success();
						}
						redirect_construct();
						err($this_word2[3]);
					}
				}
				else
				{
					if($this_right->getDeleteRestrict()!='')
					{
						$result=mysql_query("SELECT * FROM ".$table." WHERE ".$this_right->getDeleteRestrict()." and id=".$id);
						$a=mysql_fetch_array($result);
						if($a["id"]!='')
						{
							foreach($filds as $f=>$v)
							{
								if($v->getType()=="file")
								{
									uploads($v->getName(),$v->getUpload(),'delete',$id);
								}
							}
							mysql_query("DELETE FROM ".$table." WHERE id=".$id);
							if($valuestype==1)
							{
								err($this_word4[2]);
							}
							else
							{
								err($this_word2[2]);
							}
							if(function_exists('dynamic_delete_success')) {
								dynamic_delete_success();
							}
							redirect_construct();
						}
					}
					else
					{
						$result=mysql_query("SELECT * FROM ".$table." WHERE id=".$id);
						$a = mysql_fetch_array($result);
						foreach($filds as $f=>$v)
						{
							if($v->getType()=="file")
							{
								uploads($v->getName(),$v->getUpload(),'delete',$id);
							}
						}
						mysql_query("DELETE FROM ".$table." WHERE id=".$id);
						if($valuestype==1)
						{
							err($this_word4[2]);
						}
						else
						{
							err($this_word2[2]);
						}
						if(function_exists('dynamic_delete_success')) {
							dynamic_delete_success();
						}
						redirect_construct();
					}
				}
			}
			else
			{
				if($this_right->getDeleteRestrict()!='')
				{
					$result=mysql_query("SELECT * FROM ".$table." WHERE ".$this_right->getDeleteRestrict()." and id=".$id);
					$a=mysql_fetch_array($result);
					if($a["id"]!='')
					{
						uploads($ill,$t,'delete',$id);
						err('Файл успешно удален.');
						redirect_construct();
					}
				}
				else
				{
					uploads($ill,$t,'delete',$id);
					err('Файл успешно удален.');
					redirect_construct();
				}
			}
		}
	}

	if($uploads_made) {
		//загружали файл, отправляли данные стандартно
        foreach($messages_success as $message) {
	    	err($message);
		}
		foreach($messages_error as $message) {
	    	err_red($message);
		}
		foreach($messages_information as $message) {
	    	err_info($message);
		}
		if($actiontype=="change") {
			redirect_construct($id);
			redirect($redirect_path);
		}
		elseif($actiontype=="add") {
			redirect_construct();
			redirect($redirect_path);
		}
	}
	else {
		//выдаем JSON-ответ на действие, т.к. файлы не грузили, и запрос был динамический
		$json_errors=array();
		$json_fields=array();
		foreach($messages_success as $message) {
	    	$json_errors[]=array('success',$message);
		}
		foreach($messages_error as $message) {
	    	$json_errors[]=array('error',$message);
		}
		foreach($messages_information as $message) {
	    	$json_errors[]=array('information',$message);
		}
		if(isset($_SESSION['errors'])) {
			$json_errors=array_merge($json_errors,$_SESSION['errors']);
			unset($_SESSION['errors']);
		}
		if($obj->getType()==1) {
			foreach($trouble2 as $key=>$trouble_tr) {
	    		if($trouble_tr) {
	    			$json_fields[]=$key;
	    		}
			}
		}
		else {
			foreach($trouble2 as $trouble_field) {
	    		$json_fields[]=$trouble_field;
			}
		}
		dynamic_err($json_errors,$redirect_path,$json_fields);
	}
}

function cleartype3($obj,$this,$table,$id,$filds,$filds2,$valuestype)
{
	if($valuestype==0)
	{
		$result=mysql_query("SELECT * FROM ".$table." WHERE ".$obj->getParent()."=".$id." AND ".$obj->getContent()."='{menu}'");
		while($a = mysql_fetch_array($result))
		{
			cleartype3($obj,$this,$table,$a["id"],$filds,$filds2,0);
		}

		$result2=mysql_query("SELECT * FROM ".$table." WHERE ".$obj->getParent()."=".$id." AND ".$obj->getContent()."!='{menu}'");
		while($b = mysql_fetch_array($result2))
		{
			cleartype3($obj,$this,$table,$b["id"],$filds,$filds2,1);
		}

		foreach($filds as $f=>$v)
		{
			if($v->getType()=="file")
			{
				uploads($v->getName(),$v->getUpload(),'delete',$id);
			}
		}
		mysql_query("DELETE FROM ".$table." WHERE id=".$id);
	}
	else
	{
		foreach($filds2 as $f=>$v)
		{
			if($v->getType()=="file")
			{
				uploads($v->getName(),$v->getUpload(),'delete',$id);
			}
		}
		mysql_query("DELETE FROM ".$table." WHERE id=".$id);
	}
}

?>