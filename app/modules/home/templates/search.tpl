{include file="findInclude:common/templates/header.tpl" scalable=false}

{include file="findInclude:common/templates/search.tpl" emphasized=false placeholder="Search $strings.SITE_NAME"}

{foreach $federatedResults as $federatedResult}
  <h3 class="nonfocal">{$federatedResult['title']}</h3>
  {$results = $federatedResult['results']}
  {if !count($results)}
    {$noResults = array()}
    {$noResults['title'] = "No results"}
    {$results[] = $noResults}
    
  {elseif $federatedResult['total'] > count($results)}
    {$moreLink = array()}
    {$moreLink['title'] = "More results"}
    {$moreLink['url'] = $federatedResult['url']}
    {$results[] = $moreLink}
  {/if}
  {include file="findInclude:common/templates/navlist.tpl" navlistItems=$results subTitleNewline=true}
{/foreach}


{include file="findInclude:common/templates/footer.tpl"}
