<?php

$recordTable =  Doctrine_Core::getTable('Record'); 

// Busca un record por id
$records = $recordTable->find(1);

//------------------------------------------------------------------------------

//$user = $q->fetchOne();
$record = $recordTable->createQuery('r')->fetchOne();

//------------------------------------------------------------------------------

// Fetch all and get the first from collection
//$user = $q->execute()->getFirst();
$record = $recordTable->createQuery('r')->execute()->getFirst();

//------------------------------------------------------------------------------

// Obtiene un registro por el valor de un campo
// $records = $recordTable->findByX(); X: nombre del campo
$records = $recordTable->findByName();

//------------------------------------------------------------------------------

// Obtiene todos los registros existentes
$records = $recordTable->findAll();

//------------------------------------------------------------------------------

// Ejemplo de consulta donde se quiere saber el nombre de los usuarios que 
// realizaron comentarios para una relacion de one to many o 1:N.
$commentsTable =  Doctrine_Core::getTable('Comments');  
$comments =  $commentsTable->findAll();  
foreach($comments as $comment){  
    echo  $comment->User->name;  
}

// Ejemplo de la consulta anterior pero en el lenguaje DQL
$q =  Doctrine_Query::create()  
    ->select('c.*,u.name')  
    ->from('Comments c')  
    ->innerJoin('c.User u');  
$comments =  $q->execute();

//------------------------------------------------------------------------------

// Ejemplo de acceso a registros relacionados en Doctrine es fácil: puede 
// utilizar exactamente los mismos getters y setters que para las propiedades de registro.
$user = new User();

// Set
$user['username'] = 'jwage';
$user['password'] = 'changeme';

// Get
$email = $user->Email;

$email = $user->get('Email');

$email = $user['Email']; // Email el campo que relaciona a la clase User con Email.

//------------------------------------------------------------------------------

// Ejemplo de consulta entre tablas relacionadas con el lenguaje DQL.
$q = Doctrine_Query::create()
        ->from('User u')
        ->leftJoin('u.Email e')
        ->leftJoin('u.Phonenumbers p')
        ->where('u.id = ?', 1);

$user = $q->fetchOne();

echo $user->Email['address'];

echo $user->Phonenumbers[0]['phonenumber'];

// Igual a lo anterior
$q = Doctrine_Core::getTable('User')
        ->createQuery('u')
        ->leftJoin('u.Email e')
        ->leftJoin('u.Phonenumbers p')
        ->where('u.id = ?', 1);

$user = $q->fetchOne();

echo $user->Email['address'];

echo $user->Phonenumbers[0]['phonenumber'];

//------------------------------------------------------------------------------

// Determinar si existe alguna relacion con un registro en la tabla Email, para
// una realcion one to one o 1:1.
if ($user->Email->exists()) {
    // User has e-mail
} else {
    // User does not have a e-mail
}

$user->clearRelated('Email');

//Podemos simplificar aún más el escenario anterior utilizando el 
//relatedExistsmétodo. Esto es para que usted pueda hacer el cheque anterior 
//con menos código y no tiene que preocuparse por borrar la referencia 
//innecesaria después.

if ($user->relatedExists('Email')) {
    // User has e-mail
} else {
    //  User does not have a e-mail
}

//------------------------------------------------------------------------------

//Eliminación de registros relacionados

//Puede borrar los registros relacionados de forma individual al hacer una 
//llamada deleteen un registro o en una colección.

//Aquí puede borrar un registro relacionado individual:
$user->Email->delete();

//Puede eliminar un registro individual de una colección de registros:
$user->Phonenumbers[3]->delete();

//Usted podría suprimir la colección entera si usted quiso:
$user->Phonenumbers->delete();

//O simplemente puede eliminar todo el usuario y todos los objetos relacionados:
$user->delete();

//La eliminación de la dada Phonenumberspara el ID de usuario dado se puede 
//lograr de la siguiente manera:
$q = Doctrine_Query::create()
        ->delete('Phonenumber')
        ->addWhere('user_id = ?', 5)
        ->whereIn('id', array(1, 2, 3));

$numDeleted = $q->execute();

//A veces puede que no quiera eliminar los Phonenumberregistros, sino simplemente
// desvincular las relaciones estableciendo los campos de clave externa en null. 
// Esto, por supuesto, puede lograrse con DQL, pero tal vez a la manera más 
// elegante de hacer esto es mediante el uso Doctrine_Record::unlink

//Digamos que tenemos un Userque tiene tres Phonenumbers(con identificadores 1, 2 y 3). 
//Ahora desvincular el Phonenumbers1 y el 3 se puede lograr tan fácilmente como:
$user->unlink('Phonenumbers', array(1, 3));

echo $user->Phonenumbers->count(); // 1

//------------------------------------------------------------------------------

// Relacion Many to Many
//Digamos que tenemos dos clases Usery Groupque están enlazadas a través de una 
//GroupUserclase de asociación. 
$user = new User();
$user->username = 'Some User';
$user->Groups[0]->username = 'Some Group';
$user->Groups[1]->username = 'Some Other Group';
$user->save();

// Si se enlazan registros existentes
$groupUser = new GroupUser();
$groupUser->user_id = $userId;
$groupUser->group_id = $groupId;
$groupUser->save();

//------------------------------------------------------------------------------

// Relacion Many to Many
// Eliminación de un vínculo

//La forma correcta de eliminar vínculos entre muchos a muchos registros asociados 
//es mediante la instrucción DQL DELETE.
$q = Doctrine_Query::create()
        ->delete('UserGroup')
        ->addWhere('user_id = ?', 5)
        ->whereIn('group_id', array(1, 2));

$deleted = $q->execute();

//Otra forma de unlinkrelacionar objetos relacionados es a través del 
//Doctrine_Record::unlinkmétodo. Sin embargo, debe evitar utilizar este método 
//a menos que ya tenga el modelo principal, ya que implica consultar primero 
//la base de datos.
$user = Doctrine_Core::getTable('User')->find(5);
$user->unlink('Group', array(1, 2));
$user->save();

//También puede desvincular TODAS las relaciones con Groupomitiendo el segundo 
//argumento:
$user->unlink('Group');

//------------------------------------------------------------------------------

// Ejemplo innerJoin
$user = Doctrine_Core::getTable('User')
            ->createQuery('u')
            ->innerJoin('u.Profile p')
            ->where('p.username = ?', 'jwage')
            ->fetchOne();

echo $user->first_name . ' ' . $user->last_name;

//------------------------------------------------------------------------------























