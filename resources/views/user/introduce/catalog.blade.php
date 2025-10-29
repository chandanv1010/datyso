<style>
  table {
      font-family: arial, sans-serif;
      border-collapse: collapse;
      width: 100%;
  }

  td, th {
      border: 1px solid #dddddd;
      text-align: left;
      padding: 8px;
  }

  tr:nth-child(even) {
      background-color: #dddddd;
  }

  .text-center {
      text-align: center;
  }
</style>

<table>
    {{-- header --}}
    <tr>
        <th width="85%">Catalog</th>
        <th class="text-center">Link download</th>
    </tr>

    {{-- body --}}
    <tr>
        <td>Catalog 1</td>
        <td class="text-center"><a href="#">Download</a></td>
    </tr>
    <tr>
        <td>Catalog 2</td>
        <td class="text-center"><a href="#">Download</a></td>
    </tr>
    <tr>
        <td>Catalog 3</td>
        <td class="text-center"><a href="#">Download</a></td>
    </tr>
    <tr>
        <td>Catalog 4</td>
        <td class="text-center"><a href="#">Download</a></td>
    </tr>
</table>
