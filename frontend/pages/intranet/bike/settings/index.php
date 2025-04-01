<form action="{{form:url:full}}" method="post" id="frm{{page:id}}" data-prefill>
    <div class="card col-12 col-lg-4 mx-auto">
        <div class="card-body">
            <div class="col-12 mb-3">
                <label class="form-label" for="lastPayDate">Laatste uitbetalingsdatum</label>
                <div class="input-icon">
                    <span class="input-icon-addon"><i class="icon ti ti-calendar"></i></span>
                    <input role="datepicker" class="form-control" id="lastPayDate" name="lastPayDate" required>
                </div>
            </div>

            <div class="col-12">
                <label class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="block.past.enabled" name="block.past.enabled">
                    <span class="form-check-label">Blokkeer registraties in het verleden</span>
                </label>
            </div>

            <div class="col-12 mb-3">
                <select name="block.past.amount" id="block.past.amount">
                    <option value="">Selecteer hoeveelheid...</option>

                    <optgroup label="Dagen">
                        <option value="1 day">1 dag</option>
                        <option value="2 days">2 dagen</option>
                        <option value="3 days">3 dagen</option>
                        <option value="4 days">4 dagen</option>
                        <option value="5 days">5 dagen</option>
                        <option value="6 days">6 dagen</option>
                    </optgroup>

                    <optgroup label="Weken">
                        <option value="1 week">1 week</option>
                        <option value="2 weeks">2 weken</option>
                        <option value="3 weeks">3 weken</option>
                        <option value="4 weeks">4 weken</option>
                    </optgroup>

                    <optgroup label="Maanden">
                        <option value="1 month">1 maand</option>
                        <option value="2 months">2 maanden</option>
                        <option value="3 months">3 maanden</option>
                        <option value="4 months">4 maanden</option>
                        <option value="5 months">5 maanden</option>
                        <option value="6 months">6 maanden</option>
                        <option value="7 months">7 maanden</option>
                        <option value="8 months">8 maanden</option>
                        <option value="9 months">9 maanden</option>
                        <option value="10 months">10 maanden</option>
                        <option value="11 months">11 maanden</option>
                    </optgroup>

                    <optgroup label="Jaren">
                        <option value="1 year">1 jaar</option>
                        <option value="2 year">2 jaren</option>
                        <option value="3 year">3 jaren</option>
                    </optgroup>
                </select>
            </div>

            <div class="col-12">
                <label class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="block.future.enabled" name="block.future.enabled">
                    <span class="form-check-label">Blokkeer registraties in de toekomst</span>
                </label>
            </div>

            <div class="col-12">
                <select name="block.future.amount" id="block.future.amount">
                    <option value="">Selecteer hoeveelheid...</option>

                    <optgroup label="Dagen">
                        <option value="0">0 dagen</option>
                        <option value="1 day">1 dag</option>
                        <option value="2 days">2 dagen</option>
                        <option value="3 days">3 dagen</option>
                        <option value="4 days">4 dagen</option>
                        <option value="5 days">5 dagen</option>
                        <option value="6 days">6 dagen</option>
                    </optgroup>

                    <optgroup label="Weken">
                        <option value="1 week">1 week</option>
                        <option value="2 weeks">2 weken</option>
                        <option value="3 weeks">3 weken</option>
                        <option value="4 weeks">4 weken</option>
                    </optgroup>

                    <optgroup label="Maanden">
                        <option value="1 month">1 maand</option>
                        <option value="2 months">2 maanden</option>
                        <option value="3 months">3 maanden</option>
                        <option value="4 months">4 maanden</option>
                        <option value="5 months">5 maanden</option>
                        <option value="6 months">6 maanden</option>
                        <option value="7 months">7 maanden</option>
                        <option value="8 months">8 maanden</option>
                        <option value="9 months">9 maanden</option>
                        <option value="10 months">10 maanden</option>
                        <option value="11 months">11 maanden</option>
                    </optgroup>

                    <optgroup label="Jaren">
                        <option value="1 year">1 jaar</option>
                        <option value="2 year">2 jaren</option>
                        <option value="3 year">3 jaren</option>
                    </optgroup>
                </select>
            </div>
        </div>
    </div>
</form>