<% if PaginatedItems.MoreThanOnePage %>
	<div id="pageNumbers">
		<p>
			<% if PaginatedItems.NotFirstPage %>
				<a class="pages prev" href="$PaginatedItems.PrevLink" title="View the previous page">&lt;</a>
			<% end_if %>

	    	<% loop PaginatedItems.PaginationSummary(4) %>
				<% if CurrentBool %>
					<span class="pages current">$PageNum</span>
				<% else %>
					<% if Link %>
						<a class="pages" href="$Link" title="View page number $PageNum">$PageNum</a>
					<% else %>
					<span class="pages">&hellip;</span>
					<% end_if %>
				<% end_if %>
			<% end_loop %>

			<% if PaginatedItems.NotLastPage %>
				<a class="pages next" href="$PaginatedItems.NextLink" title="View the next page">&gt;</a>
			<% end_if %>
		</p>
	</div>
<% end_if %>
