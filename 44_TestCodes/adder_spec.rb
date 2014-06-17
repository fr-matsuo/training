#require '/usr/local/bin/rspec'
require '/home/ec2-user/public_html/training/44_TestCodes/Adder.rb'

describe "calc x y" do
    describe 'addTest' do
        x = 1
        y = 2
        adder = Adder.new()

        it "should be equals 3" do
            expect(adder.add(x, y)).to eq 3
        end  
    end
end
