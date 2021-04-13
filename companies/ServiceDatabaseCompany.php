<?php

 class ServiceDatabaseCompany{   

    public $directory;
    private $context;

    public function __construct($isRoot = false){

        $prefijo = ($isRoot) ? "" : "../";
        $this->directory = "{$prefijo}database";
       
        $this->context = new HeroesContext($this->directory);      
    }

    public function Add($item){

        $query = $this->context->db->prepare("insert into companies (Name) values(?)");
        $query->bind_param("s",$item->Name);
        $query->execute();
        $query->close();
    }

    public function Edit($item){     

        $query = $this->context->db->prepare("update companies set Name = ? where Id = ?");
        $query->bind_param("si",$item->Name,$item->Id);
        $query->execute();
        $query->close();            
    }

    public function Delete($id){
        $query = $this->context->db->prepare("delete from companies where Id = ?");
        $query->bind_param("i",$id);
        $query->execute();
        $query->close();
    }

    public function GetById($id){
        $company = null;

        $query = $this->context->db->prepare("select * from companies where Id = ?");
        $query->bind_param("i",$id);
        $query->execute();

        $result = $query->get_result();

        if($result->num_rows === 0){
            $query->close();
            return null;
        }else{          
            
            $row = $result->fetch_object();
            $company = new Company($row->Id,$row->Name);            
        }

        $query->close();
        return $company;    
    }

    public function GetList(){

        $companies = array();

        $query = $this->context->db->prepare("select * from companies");
        $query->execute();

        $result = $query->get_result();

        if($result->num_rows === 0){
            $query->close();
            return array();
        }else{
            while($row = $result->fetch_object()){

                $company = new Company($row->Id,$row->Name);

                array_push( $companies,$company);
            }
        }
        $query->close();
        return $companies;    
    }  
   
}
