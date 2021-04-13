<?php

 class ServiceDatabase{   

    public $directory;
    private $context;
    private $emailHandler;

    public function __construct($isRoot = false){

        $prefijo = ($isRoot) ? "" : "../";
        $this->directory = "{$prefijo}database";

        $this->emailHandler = new EmailHandler();       
        $this->context = new HeroesContext($this->directory);      
    }

    public function Add($item){

        $query = $this->context->db->prepare("insert into heroes (Name,Description,CompanyId,Status) values(?,?,?,?)");
        $query->bind_param("ssii",$item->Name,$item->Description,$item->CompanyId,$item->Status);
        $query->execute();
        $query->close();

        $this->emailHandler->SendEmail("leonardotv.93@gmail.com","Prueba","Se agrego el heroe de nombre <strong>{$item->Name}</strong>");
    }

    public function Edit($item){     

        $query = $this->context->db->prepare("update heroes set Name = ?,Description = ?,CompanyId = ?, Status = ? where Id = ?");
        $query->bind_param("ssiii",$item->Name,$item->Description,$item->CompanyId,$item->Status,$item->Id);
        $query->execute();
        $query->close();            
    }

    public function Delete($id){
        $query = $this->context->db->prepare("delete from heroes where Id = ?");
        $query->bind_param("i",$id);
        $query->execute();
        $query->close();
    }

    public function GetById($id){
        $hero = null;

        $query = $this->context->db->prepare("select * from heroes where Id = ?");
        $query->bind_param("i",$id);
        $query->execute();

        $result = $query->get_result();

        if($result->num_rows === 0){
            $query->close();
            return null;
        }else{          
            
            $row = $result->fetch_object();
            $hero = new Hero($row->Id,$row->Name,$row->Description,$row->CompanyId,$row->Status);            
        }

        $query->close();
        return $hero;    
    }

    public function GetList(){

        $heroes = array();

        $query = $this->context->db->prepare("select h.*,c.Name as 'CompanyName' from heroes h inner join companies c on h.CompanyId = c.Id");
        $query->execute();

        $result = $query->get_result();

        if($result->num_rows === 0){
            $query->close();
            return array();
        }else{
            while($row = $result->fetch_object()){

                $hero = new Hero($row->Id,$row->Name,$row->Description,$row->CompanyId,$row->Status);
                $hero->CompanyName = $row->CompanyName;
                array_push( $heroes,$hero);
            }
        }
        $query->close();
        return $heroes;    
    }  
   
}
