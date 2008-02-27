<?php

/*##################################################
 *                              search.class.php
 *                            -------------------
 *   begin                : February 1, 2008
 *   copyright            : (C) 2008 Rouchon Lo�c
 *   email                : horn@phpboost.com
 *
 *
###################################################
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
###################################################*/

define('NB_LINES', 10);
define('CACHE_TIME', 30);
define('CACHE_TIMES_USED', 10);

class Search
{
    //----------------------------------------------------------------- PUBLIC
    //----------------------------------------------------- M�thodes publiques
    function InsertResults ( &$requests )
    /**
     *  Enregistre les r�sultats de la recherche dans la base des r�sultats
     *  si ils n'y sont pas d�j�
     *  Nb requ�tes : 1
     */
    {
        global $Sql;
		
		$nbReqSEARCH = 0;
        $reqSEARCH = '';
        
        $nbReqInsert = 0;
        $reqInsert = '';
        
        // V�rification de la pr�sence des r�sultats dans le cache
        foreach ( $requests as $moduleName => $request )
        {
            if ( !$this->IsInCache ( $moduleName ) )
            {   // Si les r�sultats ne sont pas dans le cache.
                // Ajout des r�sultats dans le cache
                if ( $nbReqSEARCH > 0 )
                { $reqSEARCH .= ' UNION '; }
                else
                { $nbReqSEARCH++; }
                $reqSEARCH .= trim( $request, ' ;' );
            }
        }
        // V�rification de la pr�sence de la requ�te
        if ( $reqSEARCH != "" )
        {
            $reqSEARCH = "INSERT ( ".$reqSEARCH." ) INTO ".PREFIX."search_results;";
            $Sql->Query_inject($reqSEARCH, __LINE__, __FILE__ );
        }
        
        // Au cas ou le insert select into ne soit pas portable.
//         $reqInsert = '';
//         $request = $Sql->Query_while( $reqSEARCH, __LINE__, __FILE__ );
//         while( $result = $Sql->Sql_fetch_assoc($request) )
//         {
//             $reqInsert .= " ('".$this->id_search[$moduleName]."','".$moduleName."','".$result['id_content']."',";
//             $reqInsert .= "'".$result['relevance']."','".$result['id_content']."'), ";
//             
//             // Ex�cution de 10 requ�tes d'insertions
//             if ( $nbReqInsert == 10 )
//             {
//                 $Sql->Query_insert("INSERT INTO ".PREFIX."search_results VALUES ( ".$reqInsert." )", __LINE__, __FILE__);
//                 $reqInsert = '';
//                 $nbReqInsert = 0;
//             }
//             else { $nbReqInsert++; }
//         }
//         
//         // Ex�cution des derni�res requ�tes d'insertions
//         if ( $nbReqInsert > 1 )
//         { $Sql->Query_inject("INSERT INTO ".PREFIX."search_results VALUES ( ".$reqInsert." )", __LINE__, __FILE__); }
    }
    
    function GetResults ( &$results, &$moduleNames, $offset = 0, $nbLines = NB_LINES)
    /**
     *  Renvoie le nombre de r�sultats de la recherche
     *  et mets les r�sultats dans le tableau &results
     *  Nb requ�tes : 1, 2 si le SGBD ne supporte pas 'sql->Sql_num_rows'
     */
    {
        global $Sql;

        $results = Array ( );
        $numModules = 0;
        $modulesConditions = '';
        
        // Construction des conditions de recherche
        foreach ( $moduleNames as $moduleName )
        {
            // Teste l'existence de la recherche dans la base sinon signale l'erreur
            if ( in_array($moduleName, array_keys($this->id_search)) )
            {
                // Conditions de la recherche
                if ( $numModules > 0 )
                    $modulesConditions .= ", ";
                $modulesConditions .= $this->id_search[$moduleName];
                $numModules++;
            }
        }
        
        // R�cup�ration des $nbLines r�sultats � partir de l'$offset
        $reqResults  = "SELECT module, id_content, relevance, link
                        FROM ".PREFIX."search_index idx, ".PREFIX."search_results rst
                        WHERE (idx.id_search = rst.id_search) ";
        if ( $modulesConditions != '' )
            $reqResults .= " AND rst.id_search  IN (".$modulesConditions.")";
        $reqResults .= " ORDER BY relevance DESC ".$Sql->Sql_limit($offset, $nbLines);
        
        // Ex�cution de la requ�te
        $request = $Sql->Query_while( $reqResults, __LINE__, __FILE__ );
        while( $result = $Sql->Sql_fetch_assoc($request) )
        {   // Ajout des r�sultats
            array_push($results, $result);
        }
        // R�cup�ration du nombre de r�sultats correspondant � la recherche
        $reqNbResults  = "SELECT COUNT(*) ".PREFIX."search_results ".$modulesConditions;
        $nbResults = $Sql->Sql_num_rows( $request, $reqNbResults );
        
        //On lib�re la m�moire
        $Sql->Close($request);
        
        return $nbResults;
    }
    
    function ModulesInCache ( )
    /**
     *  Renvoie la liste des modules pr�sent dans le cache
     *  Nb requ�tes : 0
     */
    {
        return array_keys($this->id_search);
    }
    
    function IsInCache ( $moduleName )
    /**
     *  Renvoie true si les r�sultats du module sont dans le cache
     *  Nb requ�tes : 0
     */
    {
        return in_array($moduleName, array_keys($this->id_search));
    }
    
    //---------------------------------------------------------- Constructeurs
    
    function Search ( $search = '', $modules = array() )
    /**
     *  Constructeur de la classe Search
     *  Nb requ�tes : 5 + k / 10
     *  avec k nombre de module n'ayant pas de cache de recherche
     */
    {
        global $Sql, $Member;
        
        $this->errors = 0;
        $this->search = $search;
        $this->modules = $modules;
        $this->id_search = array();

        $this->id_user = $Member->Get_attribute('user_id');
        $this->modulesConditions = $this->getModulesConditions($this->modules);

        // D�lestage
        $reqDelete  = "DELETE FROM ".PREFIX."search_index WHERE ";
        $reqDelete .= "last_search_use < '".(time() - (CACHE_TIME * 60))."' OR times_used > '".CACHE_TIMES_USED."' ";
        $Sql->Query_inject($reqDelete, __LINE__, __FILE__);
        
        // V�rifications des r�sultats dans le cache.
        $reqCache  = "SELECT id_search, module FROM ".PREFIX."search_index WHERE ";
        $reqCache .= "search='".$search."' AND id_user='".$this->id_user."'";
        if ( $this->modulesConditions != '' )
            $reqCache .= " AND ".$this->modulesConditions;
        
        $request = $Sql->Query_while( $reqCache, __LINE__, __FILE__ );
        while( $row = $Sql->Sql_fetch_assoc($request) )
        {   // Ajout des r�sultats s'ils font partie de la liste des modules � traiter
            $this->id_search[$row['module']] = $row['id_search'];
        }
        $Sql->Close($request);
        
        // Mise � jours des r�sultats du cache
        if ( count ( $this->id_search ) > 0 )
        {
            $reqUpdate  = "UPDATE ".PREFIX."search_index SET times_used=(times_used + 1), last_search_use='".time()."' WHERE ";
            $reqUpdate .= "id_search IN ( ".implode($this->id_search)." ) AND user_id='".$this->id_user."';";
            $Sql->Query_inject($reqUpdate, __LINE__, __FILE__);
        }
        
        $nbReqInsert = 0;
        $reqInsert = '';
        // Pour chaque module n'�tant pas dans le cache
        foreach ( $modules as $moduleName => $options)
        {
            if ( !$this->IsInCache($moduleName) )
            {
                $reqInsert .= "('".$this->id_user."','".$moduleName."','".$search."','".$options."','".time()."', '0'),";
                // Ex�cution de 10 requ�tes d'insertions
                if ( $nbReqInsert == 10 )
                {
                    $reqInsert = "INSERT INTO ".PREFIX."search_index (`id_user`, `module`, `search`, `options`, `last_search_use`, `times_used`) VALUES ".$reqInsert."";
                    $Sql->Query_insert($reqInsert, __LINE__, __FILE__);
                    $reqInsert = '';
                    $nbReqInsert = 0;
                }
                else { $nbReqInsert++; }
            }
        }
        // Ex�cution des derni�res requ�tes d'insertions
        if ( $nbReqInsert > 1 )
        { $Sql->Query_inject("INSERT INTO ".PREFIX."search_index (`id_user`, `module`, `search`, `options`, `last_search_use`, `times_used`) VALUES ".substr($reqInsert, 0, strlen($reqInsert) - 1)."", __LINE__, __FILE__); }
        
        // R�cup�ration des r�sultats et de leurs id dans le cache.
        
        // Pourquoi faire �� plut�t que de r�cup�rer id_search pour chaque
        // insertion dans l'index du cache.
        // parce que cela donne au total pour le contructeur une complexit�
        // en requ�te de :
        // 1 (delete) + 1 (recup id) + 1 (update timestamp) + k / 10 (nb non dans le cache) + 1 (recup id) = 4 + k/10
        // au lieu de :
        // 1 (delete) + 1 (recup id) + 1 (update timestamp) + k (nb non dans le cache) = 3 + k
        // cela permet donc de grouper les insertions dans l'index du cache.
        
        // V�rifications des r�sultats dans le cache.
        $reqCache  = "SELECT id_search, module FROM ".PREFIX."search_index WHERE ";
        $reqCache .= "search='".$search."' AND id_user='".$this->id_user."'";
        if ( $this->modulesConditions != '' )
            $reqCache .= " AND ".$this->modulesConditions;
        
        $request = $Sql->Query_while( $reqCache, __LINE__, __FILE__ );
        while( $row = $Sql->Sql_fetch_assoc($request) )
        {   // Ajout des r�sultats s'ils font partie de la liste des modules � traiter
            $this->id_search[$row['module']] = $row['id_search'];
        }
        $Sql->Close($request);
    }
    
    //------------------------------------------------------------------ PRIVE
    /**
     *  Pour des raisons de compatibilit� avec PHP 4, les mots-cl�s private,
     *  protected et public ne sont pas utilis�.
     *  
     *  L'appel aux m�thodes et/ou attributs PRIVE/PROTEGE est donc possible.
     *  Cependant il est strictement d�conseill�, car cette partie du code
     *  est suceptible de changer sans avertissement et donc vos modules ne
     *  fonctionnerai plus.
     *  
     *  Bref, utilisation et vos risques et p�rils !!!
     *  
     */
    
    //----------------------------------------------------- M�thodes prot�g�es
    function getModulesConditions ( &$modules )
    /**
     *  G�n�re les conditions de la clause WHERE pour limiter les requ�tes
     *  aux seuls modules avec les bonnes options de recherches concern�s.
     */
    {
        $nbModules = count( $modules );
        
        $modulesConditions = '';
        
        if ( $nbModules > 0 )
        {
            $modulesConditions .= " ( ";
            $i = 0;
            foreach ( $modules as $moduleName => $options )
            {
                $modulesConditions .= "( module='".$moduleName."'";
                $modulesConditions .= " AND options='".$options."'";
                $modulesConditions .= " )";
                
                if ( $i < ( $nbModules - 1 ) )
                    $modulesConditions .= " OR ";
                else
                    $modulesConditions .= " ) ";
                $i++;
            }
        }
        
        return $modulesConditions;
    }
    
    //----------------------------------------------------- Attributs prot�g�s
    var $id_search;
    var $search;
    var $modules;
    var $modulesConditions;
    var $id_user;
    var $errors;

}

?>

