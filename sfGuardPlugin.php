<?php

// Creacion de un usuario nuevo o actualizacion de sus datos.

$data = array(
  'username' => '',
  'first_name' => '',
  'last_name' => '',
  'email_address' => '',
  'password' => '',
  'celular' => ''
  );

$user = Doctrine_Core::getTable('sfGuardUser')->findOrCreateBy([ 'username' => $data['username'] ]);
$user->fromArray( $data);
if($user->isNew()){
  $user->fromArray([ 'password' => $data['password'] ]);
  $user->Profile->set('celular', $data['celular']);
}

$user->save();
