<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"> <?php echo $content_top; ?>
      <h2><?php echo $text_edit_address; ?></h2>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" class="form-horizontal">
        <fieldset>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-first_name"><?php echo $entry_first_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="first_name" value="<?php echo $first_name; ?>" placeholder="<?php echo $entry_first_name; ?>" id="input-first_name" class="form-control" />
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-last_name"><?php echo $entry_last_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="last_name" value="<?php echo $last_name; ?>" placeholder="<?php echo $entry_last_name; ?>" id="input-last_name" class="form-control" />
            </div>
          </div>
            <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-middle_name"><?php echo $entry_middle_name; ?></label>
                <div class="col-sm-10">
                    <input type="text" name="middle_name" value="<?php echo $middle_name; ?>" placeholder="<?php echo $entry_middle_name; ?>" id="input-middle_name" class="form-control" />
                </div>
            </div>
          <div class="form-group hidden">
            <label class="col-sm-2 control-label" for="input-phone"><?php echo $entry_phone; ?></label>
            <div class="col-sm-10">
              <input type="text" name="phone" value="<?php echo $phone; ?>" placeholder="<?php echo $entry_phone; ?>" id="input-phone" class="form-control" />
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-zip_code"><?php echo $entry_zip_code; ?></label>
            <div class="col-sm-10">
              <input type="text" name="zip_code" value="<?php echo $zip_code; ?>" placeholder="<?php echo $entry_zip_code; ?>" id="input-address-1" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-region"><?php echo $entry_region; ?></label>
            <div class="col-sm-10">
              <input type="text" name="region" value="<?php echo $region; ?>" placeholder="<?php echo $entry_region; ?>" id="input-address-2" class="form-control" />
            </div>
          </div>
          <div class="form-group hidden">
            <label class="col-sm-2 control-label" for="input-city"><?php echo $entry_city; ?></label>
            <div class="col-sm-10">
              <input type="text" name="city" value="<?php echo $city; ?>" placeholder="<?php echo $entry_city; ?>" id="input-city" class="form-control" />
            </div>
          </div>
          <div class="form-group hidden">
            <label class="col-sm-2 control-label" for="input-street"><?php echo $entry_street; ?></label>
            <div class="col-sm-10">
              <input type="text" name="street" value="<?php echo $street; ?>" placeholder="<?php echo $entry_street; ?>" id="input-street" class="form-control" />
            </div>
          </div>
            <div class="form-group hidden">
                <label class="col-sm-2 control-label" for="input-house"><?php echo $entry_house; ?></label>
                <div class="col-sm-10">
                    <input type="text" name="house" value="<?php echo $house; ?>" placeholder="<?php echo $entry_house; ?>" id="input-house" class="form-control" />
                </div>
            </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-series"><?php echo $entry_series; ?></label>
              <div class="col-sm-10">
                  <input type="text" name="series" value="<?php echo $series; ?>" placeholder="<?php echo $entry_series; ?>" id="input-series" class="form-control" />
              </div>
          </div>
            <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-number"><?php echo $entry_number; ?></label>
                <div class="col-sm-10">
                    <input type="text" name="number" value="<?php echo $number; ?>" placeholder="<?php echo $entry_number; ?>" id="input-number" class="form-control" />
                </div>
            </div>
            <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-issue_date"><?php echo $entry_issue_date; ?></label>
                <div class="col-sm-10">
                    <input type="text" name="issue_date" value="<?php echo $issue_date; ?>" placeholder="<?php echo $entry_issue_date; ?>" id="input-issue_date" class="form-control" />
                </div>
            </div>
            <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-issued_by"><?php echo $entry_issued_by; ?></label>
                <div class="col-sm-10">
                    <input type="text" name="issued_by" value="<?php echo $issued_by; ?>" placeholder="<?php echo $entry_issued_by; ?>" id="input-issued_by" class="form-control" />
                </div>
            </div>
        </fieldset>
        <div class="buttons clearfix">
          <div class="pull-right">
            <input type="submit" value="<?php echo $button_continue; ?>" class="btn btn-primary" />
          </div>
        </div>
      </form>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>