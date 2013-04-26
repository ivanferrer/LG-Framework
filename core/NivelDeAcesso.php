<?php

namespace core;
interface NivelDeAcesso{

    public function permissaoDeAcesso(Modelo $identidade,$classe,$metodo);
    
}