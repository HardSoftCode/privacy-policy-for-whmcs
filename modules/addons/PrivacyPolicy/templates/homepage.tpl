<div class="card">
    <div class="card-body">
        {foreach from=$privacys item=privacy}
        <h4>{$privacy.title}</h4>
        <p>{$privacy.contents}</p>
        {/foreach}
    </div>
</div>