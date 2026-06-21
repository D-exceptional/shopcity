<?php 
// Get store details
$storeInformation = $storeModel->findOne($storeId);
$storeAvatar = $storeInformation['store_avatar'];
$storeName = $storeInformation['store_name'];
